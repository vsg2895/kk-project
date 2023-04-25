<?php namespace Admin\Http\Controllers;

use Jakten\Helpers\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Jakten\Events\NewRegistration;
use Jakten\Models\User;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\UserService;
use Shared\Http\Controllers\Controller;
use Admin\Http\Requests\{StoreUserRequest, UpdateUserRequest};
use Jakten\Repositories\Contracts\{OrganizationRepositoryContract, SchoolRepositoryContract, UserRepositoryContract};

/**
 * Class UserController
 * @package Admin\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * @var UserRepositoryContract
     */
    private $users;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var SchoolRepositoryContract
     */
    private $schools;

    /**
     * @var OrganizationRepositoryContract
     */
    private $organizations;

    /**
     * UsersController constructor.
     *
     * @param UserRepositoryContract $users
     * @param UserService $userService
     * @param SchoolRepositoryContract $schools
     * @param OrganizationRepositoryContract $organizations
     * @param KKJTelegramBotService $botService
     */
    public function __construct(UserRepositoryContract $users, UserService $userService, SchoolRepositoryContract $schools, OrganizationRepositoryContract $organizations, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->users = $users;
        $this->userService = $userService;
        $this->schools = $schools;
        $this->organizations = $organizations;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $users = $this->users->search($request);

        return view('admin::users.index', [
            'users' => $users->paginate()->appends(Input::except('page')),
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $user = $this->users->query()->withTrashed()->findOrFail($id);
        $organizations = $this->organizations->query()->get();
        $roles = Roles::getAvailableRoles();

        return view('admin::users.show', [
            'user' => $user,
            'roles' => $roles,
            'organizations' => $organizations,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $organizations = $this->organizations->query()->get();
        $roles = Roles::getAvailableRoles();

        return view('admin::users.create', [
            'roles' => $roles,
            'organizations' => $organizations,
            'initialOrganization' => $request->input('organization'),
        ]);
    }

    /**
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->storeUser($request);

        $request->session()->flash('message', 'Användare skapad!');

        event(new NewRegistration($user));

        return redirect()->route('admin::users.show', ['id' => $user->id]);
    }

    /**
     * @param $id
     * @param UpdateUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, UpdateUserRequest $request)
    {
        $user = $this->users->query()->findOrFail($id);
        $user = $this->userService->updateUser($user, $request);

        $request->session()->flash('message', 'Användare uppdaterad!');

        return redirect()->route('admin::users.show', ['id' => $user->id]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete($id)
    {
        $user = $this->users->query()->findOrFail($id);
        $this->userService->delete($user);

        return redirect()->route('admin::users.index')->with('message', 'Användare borttagen!');
    }

    public function restore($id)
    {
        /** @var User $user */
        $user = $this->users
            ->query()
            ->withTrashed()
            ->findOrFail($id);

        if ($user->trashed()) {
            return redirect()
                ->route('admin::users.show', ['id' => $user->id])
                ->with('message', $user->restore() ? 'Användaren har blivit återskapad' : 'Lyckades inte återskapa användaren');
        }

        return redirect()
            ->back();
    }
}
