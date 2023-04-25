<?php namespace Shared\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Jakten\Services\KKJTelegramBotService;
use Shared\Http\Controllers\Controller;

/**
 * Class ResetPasswordController
 * @package Shared\Http\Controllers\Auth
 */
class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/logga-in';

    /**
     * Create a new controller instance.
     *
     * @param KKJTelegramBotService $botService
     */
    public function __construct(KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->middleware('guest');
    }
}
