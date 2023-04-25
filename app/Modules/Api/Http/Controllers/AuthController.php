<?php namespace Api\Http\Controllers;

use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Jakten\Models\User;

/**
 * Class AuthController
 * @package Api\Http\Controllers
 */
class AuthController extends ApiController
{
    use AuthenticatesUsers;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser()
    {
        return $this->success(auth()->user());
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
        return $this->success($user);
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
