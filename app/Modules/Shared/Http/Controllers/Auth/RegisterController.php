<?php namespace Shared\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Jakten\Events\NewRegistration;
use Jakten\Helpers\Roles;
use Jakten\Models\User;
use Shared\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Jakten\Presenters\SimplifiedCities;
use Illuminate\Foundation\Http\FormRequest;
use Shared\Http\Requests\{StoreOrganizationRequest, StoreStudentRequest};
use Jakten\Repositories\Contracts\{CityRepositoryContract, SchoolRepositoryContract};
use Jakten\Services\{KKJTelegramBotService,
    Payment\Klarna\KlarnaService,
    OrganizationService,
    SchoolService,
    UserService};

/**
 * Class RegisterController
 * @package Shared\Http\Controllers\Auth
 */
class RegisterController extends Controller
{
    /**
     * @var CityRepositoryContract
     */
    protected $cities;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var SchoolService
     */
    private $schoolService;

    /**
     * @var SchoolRepositoryContract
     */
    private $schools;

    /**
     * @var KlarnaService
     */
    private $klarnaService;

    /**
     * Create a new controller instance.
     *
     * @param CityRepositoryContract $cities
     * @param OrganizationService $organizationService
     * @param UserService $userService
     * @param SchoolService $schoolService
     * @param SchoolRepositoryContract $schools
     * @param KlarnaService $klarnaService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(CityRepositoryContract $cities,
                                OrganizationService $organizationService,
                                UserService $userService,
                                SchoolService $schoolService,
                                SchoolRepositoryContract $schools,
                                KlarnaService $klarnaService,
                                KKJTelegramBotService $botService
    )
    {
        parent::__construct($botService);
        $this->middleware('guest');
        $this->cities = $cities;
        $this->organizationService = $organizationService;
        $this->userService = $userService;
        $this->schoolService = $schoolService;
        $this->schools = $schools;
        $this->klarnaService = $klarnaService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        return view('shared::auth.register');
    }

    /**
     * @param Request $request
     * @param SimplifiedCities $presenter
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showOrganization(Request $request, SimplifiedCities $presenter)
    {
        //$cities = $this->cities->getForSelect();
        $cities = $presenter->format($this->cities->getForSelect('schools'));

        return view('shared::auth.register_organization', [
            'cities' => $cities,
            'schoolToClaim' => $schoolToClaim = $request->input('school_id', null),
        ]);
    }

    /**
     * @param StoreOrganizationRequest $request
     * @return RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function storeOrganization(StoreOrganizationRequest $request)
    {
        $organization = $this->organizationService->storeOrganization($request);
        $user = $this->userService->storeUser($request, $organization);

        $schoolRequest = new FormRequest;
        $schoolRequest->merge($request->get('school'));
        $this->schoolService->storeSchool($schoolRequest, $organization->refresh());
        $user->fresh();

        $schoolToClaim = $request->input('claim', null);
        if ($schoolToClaim) {
            $school = $this->schools->query()->findOrFail($schoolToClaim);
            $this->schoolService->claim($school, $user);
        }

        $request->session()->flash('message', 'Ett konto har skapats till dig. Vänligen bekräfta din email genom att trycka på länken i mailet vi skickat till dig.');

        Log::info("(event) Raise new event", [
            "class" => __CLASS__,
            "event" => "NewRegistration",
            "user" => ["id" => $user->id, "email" => $user->email],
            "newOrganization" => true
        ]);

        event(new NewRegistration($user, true));

        return redirect()->route('auth::register.organization.finished');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function finishedOrganization()
    {
        return view('shared::auth.organization_finished');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showStudent()
    {
        return view('shared::auth.register_student');
    }

    /**
     * @param StoreStudentRequest $request
     * @return RedirectResponse
     */
    public function storeStudent(StoreStudentRequest $request)
    {
        $data = $request->input('role_id') !== Roles::ROLE_ORGANIZATION_USER ? $request->except('organization_id') : $request->all();

        if (!$request->input('role_id')) {
            $data['role_id'] = Roles::ROLE_STUDENT;
        }

        $user = User::create($data);

        $request->session()->flash('message', 'Ett konto har skapats till dig. Vänligen bekräfta din email genom att trycka på länken i mailet vi skickat till dig.');

        Log::info("(event) Raise new event", [
            "class" => __CLASS__,
            "event" => "NewRegistration",
            "user" => ["id" => $user->id, "email" => $user->email],
            "newOrganization" => false
        ]);

        event(new NewRegistration($user));

        return redirect()->route('auth::login');
    }
}
