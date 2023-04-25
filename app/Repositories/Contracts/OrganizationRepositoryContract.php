<?php namespace Jakten\Repositories\Contracts;

use Illuminate\Http\Request;

/**
 * Interface OrganizationRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface OrganizationRepositoryContract extends BaseRepositoryContract
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function search(Request $request);
}
