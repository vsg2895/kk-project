<?php namespace Jakten\Observers;

use Illuminate\Support\Facades\Mail;
use Jakten\Mail\ContactCreated;
use Jakten\Models\ContactRequest;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class ContactRequestObserver
 * @package Jakten\Observers
 */
class ContactRequestObserver
{
    /**
     * Listen to the ContactRequest created event.
     *
     * @param  ContactRequest $contactRequest
     *
     * @return void
     */
    public function created(ContactRequest $contactRequest)
    {
        Mail::send(new ContactCreated($contactRequest));
    }
}
