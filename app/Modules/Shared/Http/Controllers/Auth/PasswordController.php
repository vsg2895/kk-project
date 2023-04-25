<?php namespace Shared\Http\Controllers\Auth;

use Jakten\Facades\Auth;
use Jakten\Helpers\Roles;
use Jakten\Repositories\Contracts\UserRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Shared\Http\Controllers\Controller;
use Shared\Http\Requests\StorePasswordRequest;

/**
 * Class PasswordController
 * @package Shared\Http\Controllers\Auth
 */
class PasswordController extends Controller
{
    /**
     * @var UserRepositoryContract
     */
    private $users;

    /**
     * ConfirmationController constructor.
     *
     * @param UserRepositoryContract $users
     * @param KKJTelegramBotService $botService
     */
    public function __construct(UserRepositoryContract $users, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->users = $users;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('shared::auth.password');
    }

    /**
     * @param StorePasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StorePasswordRequest $request)
    {
        $password = $request->input('password');
        $user = Auth::user();

        $user->password = $password;
        $user->confirmed = true;
        $user->save();

        return redirect(Roles::getDashboardRouteForUser($user));
    }
}
