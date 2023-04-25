<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Model;
use Jakten\Models\PostMultimedia;
use Jakten\Repositories\Contracts\PostMultimediaRepositoryContract;

/**
 * Class PostMultimediaRepository
 * @package Jakten\Repositories
 */
class PostMultimediaRepository extends BaseRepository implements PostMultimediaRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return PostMultimedia::class;
    }
}
