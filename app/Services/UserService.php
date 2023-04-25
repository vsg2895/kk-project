<?php namespace Jakten\Services;

use Jakten\Helpers\Roles;
use Illuminate\Foundation\Http\FormRequest;
use Jakten\Models\{Organization, User};
use Jakten\Repositories\Contracts\UserRepositoryContract;

/**
 * Class UserService
 * @package Jakten\Services
 */
class UserService
{
    /**
     * @var UserRepositoryContract
     */
    private $users;

    /**
     * @var ModelService
     */
    private $modelService;

    /**
     * UserService constructor.
     *
     * @param UserRepositoryContract $users
     * @param ModelService $modelService
     */
    public function __construct(UserRepositoryContract $users, ModelService $modelService)
    {
        $this->users = $users;
        $this->modelService = $modelService;
    }

    /**
     * @param FormRequest $request
     * @param Organization $organization
     *
     * @return \Illuminate\Database\Eloquent\Model|User
     */
    public function storeUser(FormRequest $request, Organization $organization = null)
    {
        $data = $request->input('role_id') !== Roles::ROLE_ORGANIZATION_USER ? $request->except('organization_id') : $request->all();

        if ($organization) {
            $data['role_id'] = Roles::ROLE_ORGANIZATION_USER;
            $data['organization_id'] = $organization->id;
        } elseif (!$request->input('role_id')) {
            $data['role_id'] = Roles::ROLE_STUDENT;
        }
      
        $user = $this->modelService->createModel(User::class, $data);
        $user->save();

        return $user;
    }

    /**
     * @param User $user
     * @param FormRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model|User
     */
    public function updateUser(User $user, FormRequest $request)
    {
        $data = $request->input('role_id') !== Roles::ROLE_ORGANIZATION_USER ? $request->except('school_id', 'role_id') : $request->except('role_id');

        if (isset($data['password_old']) && $data['password_old'] === '') {
            foreach (['password', 'password_old', 'password_confirmation'] as $field) {
                unset($data[$field]);
            }
        }

        $user = $this->modelService->updateModel($user, $data);
        $user->save();

        return $user;
    }

    /**
     * @param User $user
     *
     * @throws \Exception
     */
    public function delete(User $user)
    {
        $user->delete();
    }
}
