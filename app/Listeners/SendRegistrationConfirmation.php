<?php
namespace Jakten\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Jakten\Events\NewRegistration;
use Jakten\Mail\OrganizationCreated;
use Jakten\Mail\UserCreated;
use Jakten\Services\ConfirmationTokenService;

/**
 * Class SendRegistrationConfirmation
 * @package Jakten\Listeners
 */
class SendRegistrationConfirmation
{
    /**
     * @var ConfirmationTokenService
     */
    private $confirmationTokenService;

    /**
     * SendRegistrationConfirmation constructor.
     *
     * @param ConfirmationTokenService $confirmationTokenService
     */
    public function __construct(ConfirmationTokenService $confirmationTokenService)
    {
        $this->confirmationTokenService = $confirmationTokenService;
    }

    /**
     * Handle the event.
     *
     * @param  NewRegistration $event
     *
     * @return void
     */
    public function handle(NewRegistration $event)
    {
        $user = $event->user;
        Log::info("(event) Handle event", [
            "class" => __CLASS__,
            "event" => "NewRegistration",
            "mail" => "UserCreated",
            "user" => ["id" => $user->id, "email" => $user->email],
        ]);

        // user registration confirmation
        $token = $this->confirmationTokenService->createToken($user);
        Mail::send((new UserCreated($user, $token)));

        // organization registration email
        if ($event->newOrganization) {
            Log::info("(event) Handle event", [
                "class" => __CLASS__,
                "event" => "NewRegistration",
                "mail" => "OrganizationCreated",
                "user" => ["id" => $user->id, "email" => $user->email],
            ]);

            Mail::send(new OrganizationCreated($user));
        }
    }
}
