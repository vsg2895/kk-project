<?php namespace Jakten\Repositories;

use Illuminate\Http\Request;
use Jakten\Helpers\Roles;
use Jakten\Helpers\Search;
use Jakten\Models\User;
use Jakten\Repositories\Contracts\UserRepositoryContract;

/**
 * Class UserRepository
 * @package Jakten\Repositories
 */
class UserRepository extends BaseRepository implements UserRepositoryContract
{
    /**
     * @return string
     */
    protected function model()
    {
        return User::class;
    }

    /**
     * Filters all non active admin users.
     *
     * @return $this
     */
    public function onlyAdmins()
    {
        $this->query()->where('users.role_id', Roles::ROLE_ADMIN)
            ->where('users.confirmed', 1)
            ->whereNull('users.deleted_at');

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function search(Request $request)
    {
        $this->query()->withTrashed();
        return Search::search($request->get('s'), $this->query(), 'users', function($queryBuilder, $terms) {
            $queryBuilder->where(function($query) use ($terms) {
                foreach ($terms as $str) {
                    foreach ([
                        'given_name', 
                        'family_name', 
                        'email'
                    ] as $field) {
                        $query->orWhere($field, 'like', '%' . $str . '%');
                    }
                }
            });
        }, true);
    }

    /**
     * Selection of user delivery channels
     *
     * @param string $label
     * @return $this
     */
    public function selectAdminsChannels($label = '')
    {
        $this->query()
            ->select('users.id', 'users.email', 'users.phone_number', 'notify_settings.channels')
            ->join('notify_settings', function($join)
                {
                    $join->on('notify_settings.user_id', '=', 'users.id')
                        ->where('notify_settings.channels', '<>', '');
                })
            ->join('notify_events', function($join) use ($label)
                {
                    $join->on('notify_events.id', '=', 'notify_settings.notify_id')
                        ->where('notify_events.label', '=', $label);
                })
            ->where('users.confirmed', 1)
            ->where('users.role_id', 3)
            ->whereNull('users.deleted_at');
        return $this;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function getUserByOrgId($id = 0)
    {
        $this->query()->where('users.organization_id', $id);
        return $this;
    }
}
