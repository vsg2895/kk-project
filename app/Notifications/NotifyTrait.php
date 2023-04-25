<?php namespace Jakten\Notifications;

use Jakten\Helpers\NotifyChannels;

/**
 * Trait NotifyTrait
 * @package Jakten\Notifications
 */
trait NotifyTrait {

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $user
     * @return array
     */
    public function via($user)
    {
        $via = []; $channels = json_decode($user->channels);
        if ($user->channels) {
            foreach ($channels as $channel => $val) {
                if ($key = array_search($channel, NotifyChannels::$types)) {
                    $via[] = NotifyChannels::$channels[$key];
                }
            }
        }

        return $via;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $user
     * @return array
     */
    public function toArray($user)
    {
        return [];
    }
}