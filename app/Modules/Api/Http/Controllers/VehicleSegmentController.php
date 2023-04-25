<?php namespace Api\Http\Controllers;

use Illuminate\Http\Request;
use Jakten\Repositories\Contracts\SchoolRepositoryContract;
use Jakten\Repositories\Contracts\VehicleRepositoryContract;
use Jakten\Repositories\Contracts\VehicleSegmentRepositoryContract;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class VehicleSegmentController
 * @package Api\Http\Controllers
 */
class VehicleSegmentController extends ApiController
{
    /**
     * @var VehicleRepositoryContract
     */
    private $vehicles;

    /**
     * @var VehicleSegmentRepositoryContract
     */
    private $vehicleSegments;

    /**
     * @var SchoolRepositoryContract
     */
    private $schools;

    /**
     * PriceController constructor.
     *
     * @param VehicleRepositoryContract $vehicles
     * @param VehicleSegmentRepositoryContract $vehicleSegments
     * @param SchoolRepositoryContract $schools
     * @param KKJTelegramBotService $botService
     * @internal param VehicleSegmentRepositoryContract $prices
     */
    public function __construct(VehicleRepositoryContract $vehicles, VehicleSegmentRepositoryContract $vehicleSegments, SchoolRepositoryContract $schools, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->vehicles = $vehicles;
        $this->vehicleSegments = $vehicleSegments;
        $this->schools = $schools;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request)
    {
        $vehicleId = $request->input('vehicle_id');
        $bookable = $request->input('bookable', false);

        $vehicle = $this->vehicles->query()->find($vehicleId);
        $this->vehicleSegments->forVehicle($vehicle);
        if ($bookable) {
            $this->vehicleSegments->bookable();
        }

        $segments = $this->vehicleSegments->get();

        return $this->success($segments);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForSchool($id)
    {
        $school = $this->schools->query()->findOrFail($id);
        $segments = $this->vehicleSegments->forSchool($school)->bookable()->query()->select(['vehicle_segments.*'])->orderBy('order')->get();

        return $this->success($segments);
    }
}
