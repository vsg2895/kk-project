<?php namespace Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jakten\Http\Requests\PartnerRequest;
use Jakten\Models\Asset;
use Jakten\Models\Partner;
use Jakten\Models\VehicleSegment;
use Jakten\Repositories\Contracts\CityRepositoryContract;
use Jakten\Repositories\Contracts\CourseRepositoryContract;
use Jakten\Repositories\Contracts\PartnerRepositoryContract;
use Jakten\Repositories\Contracts\SchoolRepositoryContract;
use Jakten\Repositories\Contracts\VehicleRepositoryContract;
use Jakten\Repositories\Contracts\VehicleSegmentRepositoryContract;
use Jakten\Services\Asset\AssetService;
use Jakten\Services\Asset\Strategy\ImagePartner;
use Jakten\Services\CourseService;
use Jakten\Services\KKJTelegramBotService;
use Shared\Http\Controllers\Controller;

/**
 * Class CourseController
 *
 * @property PartnerRepositoryContract partners
 * @property AssetService assetService
 * @package Admin\Http\Controllers
 */
class CourseTypeController extends Controller
{
    /**
     * @var CityRepositoryContract
     */
    private $cities;

    /**
     * @var VehicleSegmentRepositoryContract
     */
    private $vehicleSegments;

    /**
     * @var VehicleRepositoryContract
     */
    private $vehicles;

    /**
     * CoursesController constructor.
     *
     * @param CityRepositoryContract $cities
     * @param VehicleSegmentRepositoryContract $vehicleSegments
     * @param VehicleRepositoryContract $vehicles
     * @param KKJTelegramBotService $botService
     */
    public function __construct(
        CityRepositoryContract $cities,
        VehicleSegmentRepositoryContract $vehicleSegments,
        VehicleRepositoryContract $vehicles,
        KKJTelegramBotService $botService
    )
    {
        parent::__construct($botService);
        $this->cities = $cities;
        $this->vehicleSegments = $vehicleSegments;
        $this->vehicles = $vehicles;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $vehicleSegments = $this->vehicleSegments
            ->paginate();

        return view('admin::course_type.index', compact('vehicleSegments'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $vehicles = $this->vehicles->query()->get();
        return view('admin::course_type.create', compact('vehicles'));
    }

    /**
     * @param PartnerRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $vehicleSegmentData = $request->only([
            'default_price',
            'editable',
            'comparable',
            'bookable',
            'default_price',
            'sub_explanation',
            'explanation',
            'description',
            'sub_description',
            'slug',
            'title',
            'name',
            'calendar_description',
            'vehicle_id'
        ]);

        if (!array_key_exists('bookable', $vehicleSegmentData)) {
            $vehicleSegmentData['bookable'] = false;
        } else {
            $vehicleSegmentData['bookable'] = true;
        }

        if (!array_key_exists('comparable', $vehicleSegmentData)) {
            $vehicleSegmentData['comparable'] = false;
        } else {
            $vehicleSegmentData['comparable'] = true;
        }

        if (!array_key_exists('editable', $vehicleSegmentData)) {
            $vehicleSegmentData['editable'] = false;
        } else {
            $vehicleSegmentData['editable'] = true;
        }

        $vehicleSegment = $this->vehicleSegments->create($vehicleSegmentData);

        if ($vehicleSegment->id) {
            $id = $vehicleSegment->id;
            DB::statement("INSERT INTO schools_vehicle_segments (school_id, vehicle_segment_id) SELECT DISTINCT id, $id FROM schools");
        }

        return redirect()->back()->with($request->post());
    }

    /**
     * @param VehicleSegment $segment
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(VehicleSegment $segment)
    {
        $vehicles = $this->vehicles->query()->get();
        return view('admin::course_type.edit', ['vehicleSegment' => $segment, 'vehicles' => $vehicles]);
    }

    /**
     * @param Request $request
     * @param Partner $partner
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, VehicleSegment $segment)
    {
        $vehicleSegmentData = $request->only([
            'default_price',
            'editable',
            'comparable',
            'bookable',
            'default_price',
            'sub_explanation',
            'explanation',
            'description',
            'sub_description',
            'slug',
            'title',
            'name',
            'calendar_description',
            'vehicle_id'
        ]);

        if (!array_key_exists('bookable', $vehicleSegmentData)) {
            $vehicleSegmentData['bookable'] = false;
        } else {
            $vehicleSegmentData['bookable'] = true;
        }

        if (!array_key_exists('comparable', $vehicleSegmentData)) {
            $vehicleSegmentData['comparable'] = false;
        } else {
            $vehicleSegmentData['comparable'] = true;
        }

        if (!array_key_exists('editable', $vehicleSegmentData)) {
            $vehicleSegmentData['editable'] = false;
        } else {
            $vehicleSegmentData['editable'] = true;
        }

        $segment->update($vehicleSegmentData);

        return redirect()->back();
    }

    /**
     * @param Partner $partner
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(VehicleSegment $segment)
    {
        $segment->delete();
        return redirect()->back();
    }
}
