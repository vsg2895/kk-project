<?php namespace Jakten\Repositories\Contracts;

use Illuminate\Http\Request;

/**
 * Interface UserRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface UserRepositoryContract extends BaseRepositoryContract
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function search(Request $request);
}
