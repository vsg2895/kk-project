<?php namespace Jakten\Presenters;

use Illuminate\Support\Collection;
use Jakten\Models\School;

/**
 * Class SimplifiedSchools
 * @package Jakten\Presenters
 */
class SimplifiedSchools
{
    /**
     * @param Collection|School[] $schools
     *
     * @return array
     */
    public function format(Collection $schools)
    {
        return $schools->map(function (School $school) {

            return [
                'id' => $school->id,
                'name' => $school->name,
                'text' => $school->name,
                'slug' => $school->slug,
                'latitude' => $school->latitude,
                'longitude' => $school->longitude,
                'city_id' => $school->city_id,
                'city_slug' => $school->city ? $school->city->slug : '',
                'city_name' => $school->city ? $school->city->name : '',
                'zip' => $school->zip,
                'address' => $school->address,
                'coaddress' => $school->coaddress,
                'available_vehicles_ids' => $school->availableVehicles->pluck('id'),
                'default_course_description' => $school->default_course_description,
                'default_course_confirmation_text' => $school->default_course_confirmation_text,
                'top_deal' => $school->top_deal,
                'show_left_seats' => $school->show_left_seats,
                'left_seats' => $school->left_seats,
            ];
        });
    }
}
