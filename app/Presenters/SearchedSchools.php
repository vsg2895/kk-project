<?php namespace Jakten\Presenters;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Jakten\Models\School;

/**
 * Class SearchedSchools
 * @package Jakten\Presenters
 */
class SearchedSchools
{
    /**
     * @param Collection|School[] $schools
     *
     * @return Collection|School[]
     */
    public function format(Collection $schools)
    {
        return $schools->map(function (School $school) {
            $data = $school->toArray();
            $courses = $data['courses'];
            $formattedCourses = [];

            foreach ($courses as $course) {
                $formattedCourses[$course['vehicle_segment_id']][] = $course;
            }

            $data['courses'] = $formattedCourses;

            $data['image'] = null;
            $data['logo'] = null;

            if ($school->images->first()) {
                $data['image'] = $school->images->first()->thumbnailUrl;
            }

            if ($school->logo) {
                $data['logo'] = $school->logo->version('small')->path;
            } else if ($school->organization && $school->organization->logo) {
                $data['logo'] = $school->organization->logo->version('small')->path;
            }

            $activeCustomAddonsPrices = $data['custom_addons'] && array_first($data['custom_addons'])
                ? array_column(
                    array_filter($data['custom_addons'], function ($value) {
                        return (boolean)$value['active'];
                    }),
                    'price'
                )
                : [];

            $data['minimal_price'] = $activeCustomAddonsPrices ? min($activeCustomAddonsPrices) : 'NaN';

            return $data;
        });
    }
}
