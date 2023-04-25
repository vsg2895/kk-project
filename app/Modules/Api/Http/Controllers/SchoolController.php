<?php namespace Api\Http\Controllers;

use Api\Http\Requests\StoreSchoolRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jakten\Facades\Auth;
use Jakten\Models\Addon;
use Jakten\Models\CustomAddon;
use Jakten\Models\OrderItem;
use Jakten\Models\School;
use Jakten\Models\SchoolSegmentPrice;
use Jakten\Repositories\Contracts\OrderItemRepositoryContract;
use Jakten\Services\LoyaltyProgramService;
use Jakten\Services\RuleAPIService;
use Mockery\Exception;
use Jakten\Presenters\{SearchedSchools, SimplifiedSchools};
use Jakten\Repositories\Contracts\SchoolRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\SchoolService;
use MongoDB\Driver\Query;

/**
 * Class SchoolController
 * @package Api\Http\Controllers
 */
class SchoolController extends ApiController
{
    /**
     * @var SchoolRepositoryContract
     */
    private $schools;

    /**
     * @var SchoolService
     */
    private $schoolService;

    /**
     * @var OrderItemRepositoryContract
     */
    private $orderItem;

    /**
     * @var LoyaltyProgramService
     */
    private $loyaltyProgramService;

    /**
     * @var RuleAPIService
     */
    private $ruleAPIService;

    /**
     * SchoolsController constructor.
     * @param SchoolRepositoryContract $schools
     * @param SchoolService $schoolService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(
        SchoolRepositoryContract $schools,
        SchoolService $schoolService,
        KKJTelegramBotService $botService,
        OrderItemRepositoryContract $orderItem,
        LoyaltyProgramService $loyaltyProgramService,
        RuleAPIService $ruleAPIService
    )
    {
        parent::__construct($botService);
        $this->schools = $schools;
        $this->schoolService = $schoolService;
        $this->orderItem = $orderItem;
        $this->loyaltyProgramService = $loyaltyProgramService;
        $this->ruleAPIService = $ruleAPIService;
    }

    /**
     * @param SimplifiedSchools $simplifiedSchools
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(SimplifiedSchools $simplifiedSchools)
    {
        $schools = $this->schools->query()->with('city', 'availableVehicles')->orderBy('schools.name')->get();
        $schools = $simplifiedSchools->format($schools);

        return $this->success($schools);
    }

    /**
     * @param SimplifiedSchools $simplifiedSchools
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForLoggedInUser(SimplifiedSchools $simplifiedSchools)
    {
        if (Auth::check() && Auth::user()->isOrganizationUser()) {
            $schools = $this->schools->belongsTo(Auth::user()->organization)->query()->with('city')->orderBy('schools.name')->get();
            $schools = $simplifiedSchools->format($schools);

            return $this->success($schools);
        }

        return $this->error();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $presenter = new SearchedSchools();
        $limit = $request->input('limit');
        $limit = $limit ? $limit : 20;
        $paginator = $this->schoolService->search($request, $limit);
        $schools = $presenter->format(collect($paginator->items()));

        return $this->success(['schools' => $schools, 'total' => $paginator->total(), 'last_page' => $paginator->lastPage()]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function find($id)
    {
        $query = $this->schools
            ->query()
            ->select([
                'schools.*',
                'comparison_price',
                'DRIVING_LESSON',
                'RISK_ONE',
                'RISK_TWO',
            ])
//            ->join('school_calculated_prices', 'schools.id', '=', 'school_calculated_prices.school_id')
            ->leftJoin('school_calculated_prices', 'schools.id', '=', 'school_calculated_prices.school_id')
            ->with(['ratings', 'usps', 'city', 'upcomingCourses' => function ($upcomingCourses) {
                $upcomingCourses
                    ->orderBy('start_time', 'asc');
            }, 'prices', 'courses', 'logo', 'addons',  'organization.logo', 'customAddons']);
        $schools =  strpos($id, ',') ? $query->whereIn('id', explode(',', $id))->groupBy('schools.id')->get() : $query->findOrFail($id);

        if (strpos($id, ',')) {
            foreach ($schools as $key => $item) {
                $schools[$key]->courses = $schools[$key]->upcomingCourses;

                // use organization logo if school has none
                if (!$schools[$key]->logo && $schools[$key]->organization && $schools[$key]->organization->logo) {
                    $schools[$key]->logo()->associate($schools[$key]->organization->logo);
                }
            }

            $presenter = new SearchedSchools();
            $schoolsItems = $presenter->format(collect($schools));
        } else {
            $schools->courses = $schools->upcomingCourses;
            $schoolsItems = $schools;

            // use organization logo if school has none
            if (!$schoolsItems->logo && $schoolsItems->organization && $schoolsItems->organization->logo) {
                $schoolsItems->logo()->associate($schoolsItems->organization->logo);
            }
        }

        return $this->success($schoolsItems);
    }

    /**
     * @param $schoolId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSchoolLoyaltyLevel($schoolId)
    {
        $school = $this->schools->query()->withTrashed()->findOrFail($schoolId);
        $data = $this->loyaltyProgramService->getLoyaltyLevelBasedOnSchool($school);

        return $this->success($data);
    }

    /**
     * @param StoreSchoolRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreSchoolRequest $request)
    {
        $user = Auth::user();
        $school = $this->schoolService->storeSchool($request, $user->organization);

        return $this->success($school);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function claim($id)
    {
        $user = Auth::user();
        if ($user && $user->isOrganizationUser()) {
            $school = $this->schools->query()->findOrFail($id);
            $this->schoolService->claim($school, $user);

            return $this->success();
        }

        return $this->error();
    }

    /**
     * Search for schools that offer gift card
     * @param int $cityId : id for city
     * @return string
     */
    public function getGiftCardSchools(int $cityId)
    {
        $schools = $this->schoolService->getGiftCardSchools($cityId)->get();
        return $this->success($schools);
    }

    public function updateConnectedStatus($id, Request $request)
    {
        $school = $this->schools->query()->findOrFail($id);
        $connectedFieldUpdated = $request->connected_to && !$school->connected_to;
        $school->connected_to = $request->connected_to;
        if ($connectedFieldUpdated) $school->connected_at = now();
        elseif (!$request->connected_to) $school->connected_at = null;
        $school->save();

        $this->ruleAPIService->addSubscriber($school);

        if (!$request->connected_to) {
            $this->ruleAPIService->deleteTag($school->contact_email, 'connected');
        }

        return $this->success();
    }

    public function saveOrder(Request $request)
    {
        $post = $request->all();
        $order = explode('&', str_replace('segment[]=', '', $post['order']));
        $type = $post['type'];

        $i = 1;

        try {
            if((int)$type) {
                foreach ($order as $value) {
                    SchoolSegmentPrice::query()
                        ->where('school_id', $post['school_id'])
                        ->where('vehicle_segment_id', $value)
                        ->update(['sort_order' => $i]);
                    $i++;
                }
            }

            if($type === 'addons') {
                foreach ($order as $value) {

                    DB::table('schools_addons')
                        ->where('addon_id', (int)$value)->where('school_id',  $post['school_id'])
                        ->update([
                            'sort_order' => $i
                        ]);
                    $i++;
                }
            }

            if($type === 'customAddons') {
                foreach ($order as $value) {
                    CustomAddon::query()
                        ->where('id', $value)
                        ->update(['sort_order' => $i]);
                    $i++;
                }
            }


        } catch (Exception $exception) {

            $request->session()->flash('errors', 'Trafikskolan har inte uppdaterats!');
            $this->error();
        }

        $request->session()->flash('message', 'Trafikskola uppdaterad!');

        $this->success();
    }
}
