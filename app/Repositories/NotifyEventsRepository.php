<?php namespace Jakten\Repositories;

use Jakten\Facades\Auth;
use Jakten\Models\{User, NotifyEvent};
use Jakten\Repositories\Contracts\NotifyEventsRepositoryContract;

/**
 * Class NotifyEventsRepository
 * @package Jakten\Repositories
 */
class NotifyEventsRepository extends BaseRepository implements NotifyEventsRepositoryContract
{
    /**
     * @return string
     */
    protected function model()
    {
        return NotifyEvent::class;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|NotifyEventsRepositoryContract
     */
    public function active()
    {
        $this->query()->where('notify_events.available', 1);

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function byRole(User $user)
    {
        $this->query()->where('notify_events.role_id', $user->role_id);

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function getChannels(User $user)
    {
        $this->query()->leftJoin('notify_settings', function($join) use ($user)
        {
            $join->on('notify_settings.notify_id', 'notify_events.id')
                ->where('notify_settings.user_id', $user->id);
        });

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNotifyEvents()
    {
        $user = Auth::user();
        return $this->active()
            ->byRole($user)
            ->getChannels($user)
            ->select('notify_events.id', 'notify_events.label', 'notify_settings.channels')
            ->get();
    }

}