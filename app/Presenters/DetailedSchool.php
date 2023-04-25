<?php namespace Jakten\Presenters;

use Jakten\Models\School;

/**
 * Class DetailedSchool
 * @package Jakten\Presenters
 */
class DetailedSchool
{
    /**
     * @param School $school
     * @return array
     */
    public function format(School $school)
    {
        $prices = [];
        foreach ($school->availableVehicles as $vehicle) {
            $prices[] = [
                'type' => $vehicle->name,
                'label' => $vehicle->label,
                'prices' => $school->formatter()->prices($vehicle->id),
            ];
        }

        return [
            'id' => $school->id,
            'name' => $school->name,
            'text' => $school->name,
            'slug' => $school->slug,
            'latitude' => $school->latitude,
            'longitude' => $school->longitude,
            'city_id' => $school->city_id,
            'city_slug' => $school->city->slug,
            'city_name' => $school->city->name,
            'prices' => $prices,
        ];
    }
}
