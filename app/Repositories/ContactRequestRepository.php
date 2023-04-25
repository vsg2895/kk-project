<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Model;
use Jakten\Models\ContactRequest;
use Jakten\Repositories\Contracts\ContactRequestRepositoryContract;

/**
 * Class ContactRequestRepository
 * @package Jakten\Repositories
 */
class ContactRequestRepository extends BaseRepository implements ContactRequestRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return ContactRequest::class;
    }
}
