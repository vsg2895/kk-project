<?php namespace Shared\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Jakten\Facades\Auth;
use Jakten\Repositories\Contracts\UserRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Shared\Http\Controllers\Controller;

/**
 * Class ForgotPasswordController
 * @package Shared\Http\Controllers\Auth
 */
class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * @var float|int
     */
    private $expires;

    /**
     * @var UserRepositoryContract
     */
    private $users;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * Create a new controller instance.
     *
     * @param UserRepositoryContract $users
     * @param Hasher $hasher
     * @param KKJTelegramBotService $botService
     */
    public function __construct(UserRepositoryContract $users, Hasher $hasher, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);

        $this->middleware('guest');
        $this->users = $users;
        $this->hasher = $hasher;
        $this->expires = 60 * 60;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        return view('shared::auth.forgot');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws AuthenticationException
     */
    public function reset(Request $request)
    {
        $email = $request->input('email', null);
        $token = $request->input('token', null);
        $reset = \DB::table('password_resets')->where('email', $email)->first();

        if ($reset && $this->hasher->check($token, $reset->token) && !$this->tokenExpired($reset->created_at)) {
            $user = $this->users->query()->where('email', $email)->firstOrFail();
            Auth::login($user, true);
            \DB::table('password_resets')->where('email', $email)->where('token', $token)->delete();

            return redirect()->route('auth::password.create');
        } else {
            throw new AuthenticationException();
        }
    }

    /**
     * @param $createdAt
     * @return bool
     */
    protected function tokenExpired($createdAt)
    {
        return Carbon::parse($createdAt)->addSeconds($this->expires)->isPast();
    }
}
