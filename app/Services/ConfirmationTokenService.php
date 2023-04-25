<?php namespace Jakten\Services;

use Jakten\Models\ConfirmationToken;
use Jakten\Models\User;

/**
 * Class ConfirmationTokenService
 * @package Jakten\Services
 */
class ConfirmationTokenService
{
    /**
     * @param User $user
     *
     * @return ConfirmationToken
     */
    public function createToken(User $user)
    {
        $token = new ConfirmationToken();
        $token->email = $user->email;
        $token->token = str_random();
        $token->save();

        return $token;
    }

    /**
     * @param $email
     * @param $token
     * @return bool
     */
    public function authenticate($email, $token)
    {
        $token = ConfirmationToken::where('email', $email)->where('token', $token)->first();

        if ($token) {
            $token->delete();

            return true;
        } else {
            return false;
        }
    }
}
