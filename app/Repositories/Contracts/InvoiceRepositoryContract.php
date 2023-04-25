<?php namespace Jakten\Repositories\Contracts;

use Jakten\Models\Organization;
use Jakten\Models\School;

/**
 * Interface InvoiceRepositoryContract
 * @package Jakten\Repositories\Contracts
 */
interface InvoiceRepositoryContract extends BaseRepositoryContract
{
    /**
     * @param School $school
     *
     * @return \Illuminate\Database\Eloquent\Builder|OrderRepositoryContract
     */
    public function forSchool(School $school);

    /**
     * @param Organization $organization
     *
     * @return \Illuminate\Database\Eloquent\Builder|OrderRepositoryContract
     */
    public function forOrganization(Organization $organization);

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderRepositoryContract
     */
    public function notPaid();

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderRepositoryContract
     */
    public function paid();
}
