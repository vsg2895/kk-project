<?php namespace Jakten\Repositories\Contracts;

/**
 * Interface CourseRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface CourseParticipantsRepositoryContract extends BaseRepositoryContract
{
    /**
     * @return \Illuminate\Database\Eloquent\Builder|CourseParticipantsRepositoryContract
     */
    public function students();
}
