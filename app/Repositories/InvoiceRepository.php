<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Model;
use Jakten\Models\{Invoice, Organization, School};
use Jakten\Repositories\Contracts\InvoiceRepositoryContract;

/**
 * Class InvoiceRepository
 * @package Jakten\Repositories
 */
class InvoiceRepository extends BaseRepository implements InvoiceRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return Invoice::class;
    }

    /**
     * @param School $school
     *
     * @return \Illuminate\Database\Eloquent\Builder|InvoiceRepositoryContract
     */
    public function forSchool(School $school)
    {
        $this->query()->where('school_id', $school->id);

        return $this;
    }

    /**
     * @param Organization $organization
     *
     * @return \Illuminate\Database\Eloquent\Builder|InvoiceRepositoryContract
     */
    public function forOrganization(Organization $organization)
    {
        $this->query()->whereIn('school_id', $organization->schools->pluck('id')->all());

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|InvoiceRepositoryContract
     */
    public function notPaid()
    {
        $this->query()->whereNull('paid_at');

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|InvoiceRepositoryContract
     */
    public function paid()
    {
        $this->query()->whereNotNull('paid_at');

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|InvoiceRepositoryContract
     */
    public function sent()
    {
        $this->query()->whereNotNull('sent_at');

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|InvoiceRepositoryContract
     */
    public function notSent()
    {
        $this->query()->whereNull('sent_at');

        return $this;
    }
}
