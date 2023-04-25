<?php namespace Jakten\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface BaseRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface BaseRepositoryContract
{
    /**
     * @return Builder
     */
    public function query();

    /**
     * @return $this
     *
     * @throws \Exception
     */
    public function reset();
}
