<?php namespace Student\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Jakten\Facades\Auth;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\UserService;
use Shared\Http\Controllers\Controller;
use Student\Http\Requests\UpdateUserRequest;

/**
 * Class UserController
 * @package Student\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * UserController constructor.
     *
     * @param UserService $userService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(UserService $userService, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->userService = $userService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        $user = Auth::user();

        return view('shared::user.show', [
            'user' => $user,
            'type' => 'student',
        ]);
    }

    /**
     * @param UpdateUserRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(UpdateUserRequest $request)
    {
        $user = Auth::user();

        $user = $this->userService->updateUser($user, $request);

        return view('shared::user.show', [
            'user' => $user,
            'type' => 'student',
            'message' => 'Användare uppdaterad!',
        ]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete()
    {
        $this->userService->delete(Auth::user());
        Session::flush();

        return redirect()->route('shared::index.index')->with('message', 'Användare borttagen!');
    }
}
