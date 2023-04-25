<?php namespace Jakten\Repositories\Contracts;

/**
 * Interface UspsRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface UspsRepositoryContract extends BaseRepositoryContract
{
    /**
     * @return mixed
     */
    public function getSchoolId();

    /**
     * @param $ids
     * @return mixed
     */
    public function hasId($ids);
}
