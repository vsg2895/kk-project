<?php namespace Jakten\Observers;

use Jakten\Services\Payment\Klarna\KlarnaService;
use Jakten\Models\{School, SchoolSegmentPrice, VehicleSegment};
use Jakten\Repositories\Contracts\{VehicleRepositoryContract, VehicleSegmentRepositoryContract};

/**
 * Class SchoolObserver
 * @package Jakten\Observers
 */
class SchoolObserver
{
    /**
     * @var KlarnaService
     */
    private $klarnaService;

    /**
     * @var VehicleSegmentRepositoryContract
     */
    private $vehicleSegments;

    /**
     * @var VehicleRepositoryContract
     */
    private $vehicles;

    /**
     * SchoolObserver constructor.
     *
     * @param KlarnaService $klarnaService
     * @param VehicleSegmentRepositoryContract $vehicleSegments
     * @param VehicleRepositoryContract $vehicles
     */
    public function __construct(KlarnaService $klarnaService, VehicleSegmentRepositoryContract $vehicleSegments, VehicleRepositoryContract $vehicles)
    {
        $this->klarnaService = $klarnaService;
        $this->vehicleSegments = $vehicleSegments;
        $this->vehicles = $vehicles;
    }

    /**
     * Listen to the School created event.
     *
     * @param School $school
     */
    public function created(School $school)
    {
        $this->addPrices($school);
    }

    /**
     * @param School $school
     */
    private function addPrices(School $school)
    {
        $segments = $this->vehicleSegments->query()->get();

        $school->segments()->saveMany($segments);
        $prices = [];
        foreach ($segments as $segment) {
            /**
             * @var VehicleSegment
             */
            $price = new SchoolSegmentPrice();
            $price->amount = $segment->default_price;
            $price->comment = $segment->editable && $segment->default_price ? 'Priset är baserat på en schablon' : null;
            $price->quantity = $segment->default_price ? 1 : null;
            $price->vehicle_segment_id = $segment->id;
            $prices[] = $price;
        }
        $school->prices()->saveMany($prices);
    }
}
