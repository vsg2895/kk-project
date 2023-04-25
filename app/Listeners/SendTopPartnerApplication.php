<?php

namespace Jakten\Listeners;

use Illuminate\Support\Facades\Mail;
use Jakten\Events\BecomeTopPartnerApplication;
use Jakten\Mail\TopPartnerMail;

class SendTopPartnerApplication
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(BecomeTopPartnerApplication $event)
    {
        Mail::send(new TopPartnerMail($event->schoolName, $event->schoolEmail));
    }
}
