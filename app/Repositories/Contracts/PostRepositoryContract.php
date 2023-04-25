<?php namespace Jakten\Repositories\Contracts;

/**
 * Interface PostRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface PostRepositoryContract extends BaseRepositoryContract
{
    /**
     * @return \Illuminate\Database\Eloquent\Builder|PostRepositoryContract
     */
    public function isVisible();

    /**
     * @return mixed
     */
    public function getPostTypes();
}
