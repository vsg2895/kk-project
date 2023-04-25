<?php namespace Jakten\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Jakten\Models\City;

/**
 * Interface CityRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface CityRepositoryContract extends BaseRepositoryContract
{
    /**
     * @param $slug
     *
     * @return \Illuminate\Database\Eloquent\Builder|CityRepositoryContract
     */
    public function bySlug($slug);

    /**
     * @param string|array $with
     * @return Collection|City[]
     */
    public function getForSelect($with = []);

    /**
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request);
}
