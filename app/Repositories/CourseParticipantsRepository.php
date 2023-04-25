<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Model;
use Jakten\Helpers\Roles;
use Jakten\Models\CourseParticipant;
use Jakten\Repositories\Contracts\CourseParticipantsRepositoryContract;

/**
 * Class CourseParticipantsRepository
 * @package Jakten\Repositories
 */
class CourseParticipantsRepository extends BaseRepository implements CourseParticipantsRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return CourseParticipant::class;
    }

    /**
     * @return $this|\Illuminate\Database\Eloquent\Builder|CourseParticipantsRepositoryContract
     */
    public function students()
    {
        $this->query()->where('type', Roles::$roles[Roles::ROLE_STUDENT]);

        return $this;
    }
}
