<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Model;
use Jakten\Models\Annotation;
use Jakten\Repositories\Contracts\AnnotationRepositoryContract;

/**
 * Class AnnotationRepository
 * @package Jakten\Repositories
 */
class AnnotationRepository extends BaseRepository implements AnnotationRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return Annotation::class;
    }
}
