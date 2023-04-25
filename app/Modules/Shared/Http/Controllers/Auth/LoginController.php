<?php namespace Shared\Http\Controllers\Auth;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Jakten\Helpers\Roles;
use Jakten\Models\User;
use Jakten\Services\KKJTelegramBotService;
use Shared\Http\Controllers\Controller;

/**
 * Class LoginController
 * @package Shared\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @param KKJTelegramBotService $botService
     */
    public function __construct(KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Override default
     */
    public function showLoginForm()
    {
        return view('shared::auth.login');
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return mixed
     *
     * @throws AuthorizationException
     */
    protected function authenticated(Request $request, User $user)
    {
        return redirect(Roles::getDashboardRouteForUser($user));
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        $credentials['confirmed'] = 1;

        return $credentials;
    }
}
