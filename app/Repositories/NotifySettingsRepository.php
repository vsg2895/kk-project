<?php namespace Jakten\Repositories;

use Jakten\Facades\Auth;
use Jakten\Models\{User, NotifySetting, NotifyEvent};
use Jakten\Repositories\Contracts\NotifySettingsRepositoryContract;

/**
 * Class NotifySettingsRepository
 * @package Jakten\Repositories
 */
class NotifySettingsRepository extends BaseRepository implements NotifySettingsRepositoryContract
{
    /**
     * @return string
     */
    protected function model()
    {
        return NotifySetting::class;
    }

    /**
     * @param $formEvents
     * @param $notifyEvents
     * @throws \Exception
     */
    public function updateSettings($formEvents, $notifyEvents)
    {
        $user = Auth::user();

        foreach ($notifyEvents as $event) {

            if (isset($formEvents[$event->id])) {
                $channels = json_encode($formEvents[$event->id]);

                if ($event->channels != $channels) {
                    $this->updateChannels($event, $user, $channels);
                }
            } else {
                $this->updateChannels($event, $user, '');
            }

        }

    }

    /**
     * @param NotifyEvent $event
     * @param User $user
     * @param null $channels
     * @throws \Exception
     */
    protected function updateChannels(NotifyEvent $event, User $user, $channels = NULL)
    {
        if (is_null($event->channels)) {
            $this->reset()->query()->create([
                'notify_id' => $event->id,
                'user_id' => $user->id,
                'channels' => $channels
            ]);
        } else {
            $this->reset()->query()
                ->where('notify_id', $event->id)
                ->where('user_id', $user->id)
                ->update(['channels' => $channels]);
        }
    }
}