<?php namespace Jakten\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

/**
 * Class CustomMailChannel
 * @package Jakten\Channels
 */
class CustomMailChannel
{
    /**
     * Send the given mail notification.
     *
     * @param  mixed  $user
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($user, Notification $notification)
    {
        Mail::send($notification->toCustomMail($user));
    }
}