<?php namespace Jakten\Channels;

use Jakten\Jobs\SendSMSNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Jakten\Helpers\PhoneNumber;

/**
 * Class CustomSMSChannel
 * @package App\Channels
 */
class CustomSMSChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $user
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($user, Notification $notification)
    {
        $phoneNumber = preg_replace('/\s+/', '', $user->phone_number);
        if (PhoneNumber::validate($phoneNumber)) {
            $sms = [
                'from' => 'KKJ', /* Can be up to 11 alphanumeric characters */
                'to' => $phoneNumber,
                'message' => $notification->toCustomSMS()  /* 'flashsms' => 'yes' */
            ];
            dispatch(new SendSMSNotification($sms));
        } else {
            Log::warning('UserId:'. ($user ? $user->id : '') .', Phone number: '. ($user ? $user->phone_number : '') .' is not valid');
        }

    }
}