<?php namespace Jakten\Repositories\Contracts;

/**
 * Interface PageUriRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface PageUriRepositoryContract extends BaseRepositoryContract
{
    /**
     * @param $uri
     * @param bool $onlyIsActive
     *
     * @return \Illuminate\Database\Eloquent\Builder|PageUriRepositoryContract
     */
    public function byUri($uri, $onlyIsActive = false);
}
