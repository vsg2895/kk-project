<?php namespace Api\Http\Controllers;

use Jakten\Repositories\Contracts\SchoolRepositoryContract;
use Jakten\Repositories\Contracts\VehicleRepositoryContract;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class VehicleController
 * @package Api\Http\Controllers
 */
class VehicleController extends ApiController
{
    /**
     * @var VehicleRepositoryContract
     */
    private $vehicles;

    /**
     * VehicleController constructor.
     *
     * @param VehicleRepositoryContract $vehicles
     * @param KKJTelegramBotService $botService
     */
    public function __construct(VehicleRepositoryContract $vehicles, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->vehicles = $vehicles;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        $vehicles = $this->vehicles->query()->get();

        return $this->success($vehicles);
    }

    /**
     * @param $id
     * @param SchoolRepositoryContract $schools
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForSchool($id, SchoolRepositoryContract $schools)
    {
        $school = $schools->query()->findOrFail($id);
        return $this->success($school->availableVehicles);
    }

    /**
     * @param $id
     * @param SchoolRepositoryContract $schools
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrderForSchool($id, SchoolRepositoryContract $schools)
    {
        $school = $schools->query()->findOrFail($id);
        $arraySort = [];

        foreach ($school->prices->where('segment.editable', true)->groupBy('segment.vehicle_id') as $val) {
            $arraySort[$val->first()->segment->vehicle_id] = $val->sortBy('sort_order')->values()->toArray();
        }

        return $this->success($arraySort);
    }
}
