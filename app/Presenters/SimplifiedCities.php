<?php namespace Jakten\Presenters;

use Illuminate\Support\Collection;
use Jakten\Models\City;

/**
 * Class SimplifiedCities
 * @package Jakten\Presenters
 */
class SimplifiedCities
{
    /**
     * @param Collection|City[] $cities
     *
     * @return array
     */
    public function format(Collection $cities)
    {
        return $cities->map(function (City $city) {
            $info = (isset($city->info)) ?
                [
                    'trafikskolor' => $city->info->desc_trafikskolor,
                    'introduktionskurser' => $city->info->desc_introduktionskurser,
                    'riskettan' => $city->info->desc_riskettan,
                    'teorilektion' => $city->info->desc_teorilektion,
                    'risktvaan' => $city->info->desc_risktvaan,
                    'mopedkurs' => $city->info->desc_mopedkurs,
                    'riskettanmc' => $city->info->desc_riskettanmc
                ] : null;

            return [
                'id' => $city->id,
                'name' => $city->name,
                'best_deal_description' => $city->school_description,
                'slug' => $city->slug,
                'latitude' => $city->latitude,
                'longitude' => $city->longitude,
                'best_deal' => $city->bestDeals ?? null,
                'school_count' => $city->schools->count(),
                'info' => $info
            ];
        });
    }
}
