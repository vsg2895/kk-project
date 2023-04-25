<?php namespace Shared\Http\Controllers\Auth;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Jakten\Facades\Auth;
use Jakten\Repositories\Contracts\UserRepositoryContract;
use Jakten\Services\ConfirmationTokenService;
use Jakten\Services\KKJTelegramBotService;
use Shared\Http\Controllers\Controller;

/**
 * Class ConfirmationController
 * @package Shared\Http\Controllers\Auth
 */
class ConfirmationController extends Controller
{
    /**
     * @var ConfirmationTokenService
     */
    private $confirmationTokenService;

    /**
     * @var UserRepositoryContract
     */
    private $users;

    /**
     * ConfirmationController constructor.
     *
     * @param ConfirmationTokenService $confirmationTokenService
     * @param UserRepositoryContract $users
     */
    public function __construct(ConfirmationTokenService $confirmationTokenService, UserRepositoryContract $users, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);

        $this->confirmationTokenService = $confirmationTokenService;
        $this->users = $users;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws AuthenticationException
     */
    public function confirm(Request $request)
    {
        $email = $request->input('email');
        $valid = $this->confirmationTokenService->authenticate($email, $request->input('token'));

        if ($valid) {
            $user = $this->users->query()->where('email', $email)->firstOrFail();
            $user->confirmed = true;
            $user->save();
            Auth::login($user, true);

            return redirect()->route('auth::password.create');
        } else {
            throw new AuthenticationException();
        }
    }
}
