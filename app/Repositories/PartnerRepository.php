<?php

namespace Jakten\Repositories;

use Illuminate\Http\Request;
use Jakten\Models\Partner;
use Jakten\Repositories\Contracts\PartnerRepositoryContract;

class PartnerRepository extends BaseRepository implements PartnerRepositoryContract
{

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    protected function model()
    {
        return Partner::class;
    }

    /**
     * @return PartnerRepository
     */
    public function isActive()
    {
        $this->query()->where('active', true);
        return $this;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(Request $request)
    {
        return $this->query()
            ->forPage($request->query('page', 1))
            ->limit($request->query('limit', 1))
            ->paginate();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data = [])
    {
        return $this->query()->create($data);
    }

    /**
     * @param string $order
     * @param string $field
     * @return PartnerRepository
     */
    public function order(string $order = 'desc', string $field = 'id')
    {
        $this->query()->orderBy($field, $order);
        return $this;
    }
}
