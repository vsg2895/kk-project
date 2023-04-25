<?php namespace Organization\Http\Controllers;

use Illuminate\Http\Request;
use Jakten\Events\NewRegistration;
use Jakten\Facades\Auth;
use Jakten\Repositories\Contracts\UserRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\UserService;
use Organization\Http\Requests\StoreUserRequest;
use Organization\Http\Requests\UpdateUserRequest;
use Shared\Http\Controllers\Controller;

/**
 * Class UserController
 * @package Organization\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var UserRepositoryContract
     */
    private $users;

    /**
     * UserController constructor.
     *
     * @param UserService $userService
     * @param UserRepositoryContract $users
     * @param KKJTelegramBotService $botService
     */
    public function __construct(UserService $userService, UserRepositoryContract $users, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->userService = $userService;
        $this->users = $users;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $organization = Auth::user()->organization;
        $users = $this->users->query()->where('organization_id', $organization->id)->paginate();

        return view('organization::users.index', [
            'users' => $users
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('organization::users.create');
    }

    /**
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreUserRequest $request)
    {
        $organization = Auth::user()->organization;
        $user = $this->userService->storeUser($request, $organization);
        event(new NewRegistration($user));

        $request->session()->flash('message', 'Användare skapad!');

        return redirect()->route('organization::user.show', ['id' => $user->id]);
    }

    /**
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id = null)
    {
        if ($id) {
            $user = $this->users->query()->findOrFail($id);
        } else {
            $user = Auth::user();
        }

        $this->authorize('view', $user);

        return view('shared::user.show', [
            'user' => $user,
            'type' => 'organization',
        ]);
    }

    /**
     * @param null $id
     * @param UpdateUserRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update($id = null, UpdateUserRequest $request)
    {
        if ($id) {
            $user = $this->users->query()->findOrFail($id);
        } else {
            $user = Auth::user();
        }

        $this->authorize('view', $user);

        $user = $this->userService->updateUser($user, $request);

        $request->session()->flash('message', 'Användare uppdaterad!');

        return view('shared::user.show', [
            'user' => $user,
            'type' => 'organization',
        ]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function back(Request $request)
    {
        if ($request->session()->get('adminId')) {
            Auth::guard()->logout();
            Auth::loginUsingId($request->session()->get('adminId'), true);

            return redirect()->route('admin::dashboard');
        }

        return redirect()->back();
    }
}
