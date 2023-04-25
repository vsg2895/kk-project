<?php namespace Jakten\Observers;

use Jakten\Models\User;
use Jakten\Services\ConfirmationTokenService;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class UserObserver
 * @package Jakten\Observers
 */
class UserObserver
{
    /**
     * @var ConfirmationTokenService
     */
    private $confirmationTokenService;

    /** @var KKJTelegramBotService */
    private $botService;

    /**
     * UserObserver constructor.
     *
     * @param ConfirmationTokenService $confirmationTokenService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(ConfirmationTokenService $confirmationTokenService, KKJTelegramBotService $botService)
    {
        $this->confirmationTokenService = $confirmationTokenService;
        $this->botService = $botService;
    }

    /**
     * Listen to the User created event.
     *
     * @param User $user
     */
    public function created(User $user)
    {
        $this->botService->log('user_created', $user);
        // // user registration confirmation
        // $token = $this->confirmationTokenService->createToken($user);
        // Mail::send(new UserCreated($user, $token));

        // // organization registration email
        // if ($user->organization_id) {
        //     Mail::send(new OrganizationCreated($user));
        // }
    }
}
