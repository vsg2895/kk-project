<?php namespace Jakten\Facades;

use Illuminate\Support\Facades\Facade;
use Jakten\Models\User;

/**
 * @method static mixed guard(string|null $name = null)
 * @method static void shouldUse(string $name);
 * @method static bool check()
 * @method static bool guest()
 * @method static User|null user()
 * @method static int|null id()
 * @method static bool validate(array $credentials = [])
 * @method static void setUser(User $user)
 * @method static bool attempt(array $credentials = [], bool $remember = false, bool $login = true)
 * @method static bool once(array $credentials = [])
 * @method static void login(User $user, bool $remember = false)
 * @method static User loginUsingId(mixed $id, bool $remember = false)
 * @method static bool onceUsingId(mixed $id)
 * @method static bool viaRemember()
 * @method static void logout()
 *
 * @see \Illuminate\Auth\AuthManager
 * @see \Illuminate\Contracts\Auth\Factory
 * @see \Illuminate\Contracts\Auth\Guard
 * @see \Illuminate\Contracts\Auth\StatefulGuard
 */
class Auth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'auth';
    }

    /**
     * Register the typical authentication routes for an application.
     *
     * @return void
     */
    public static function routes()
    {
        static::$app->make('router')->auth();
    }
}
