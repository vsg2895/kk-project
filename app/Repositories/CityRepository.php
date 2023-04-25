<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Jakten\Helpers\Search;
use Jakten\Models\City;
use Jakten\Repositories\Contracts\CityRepositoryContract;

/**
 * Class CityRepository
 * @package Jakten\Repositories
 */
class CityRepository extends BaseRepository implements CityRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return City::class;
    }

    /**
     * @param $slug
     *
     * @return \Illuminate\Database\Eloquent\Builder|CityRepositoryContract
     */
    public function bySlug($slug)
    {
        $this->query()->where('slug', $slug);

        return $this;
    }

    /**
     * @param string|array $with
     * @return Collection|City[]
     */
    public function getForSelect($with = [])
    {
        $cities = $this->query()->with($with)->orderBy('name')->get();
        foreach ($cities as $index => $city) {
            if ($index === 0 || $index === 1 || $index === 2) {
                $cities->push($city);
            }
            if ($city->name === 'Stockholm') {
                $cities->put(0, $city);
            } elseif ($city->name === 'Göteborg') {
                $cities->put(1, $city);
            } elseif ($city->name === 'Malmö') {
                $cities->put(2, $city);
            }
        }

        return $cities->unique('id');
    }

    public function search(Request $request)
    {
        $this->query();
        return Search::search($request->get('s'), $this->query(), 'cities', function($queryBuilder, $terms) {
            $queryBuilder->where(function($query) use ($terms) {
                foreach ($terms as $str) {
                    foreach ([
                                 'name',
                                 'slug'
                             ] as $field) {
                        $query->orWhere($field, 'like', '%' . $str . '%');
                    }
                }
            });
        }, true);
    }
}
