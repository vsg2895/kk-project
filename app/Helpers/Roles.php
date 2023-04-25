<?php namespace Jakten\Helpers;

use Jakten\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * Class Roles
 * @package Jakten\Helpers
 */
class Roles
{
    /**
     * Roles constants
     */
    const ROLE_STUDENT = 1;
    const ROLE_ORGANIZATION_USER = 2;
    const ROLE_ADMIN = 3;

    /**
     * User roles
     *
     * @var array
     */
    public static $roles = [
        self::ROLE_STUDENT => 'student',
        self::ROLE_ORGANIZATION_USER => 'organization',
        self::ROLE_ADMIN => 'admin'
    ];

    /**
     * @return Collection
     */
    public static function getAvailableRoles()
    {
        return collect([
            'student' => self::ROLE_STUDENT,
            'school_employee' => self::ROLE_ORGANIZATION_USER,
            'admin' => self::ROLE_ADMIN,
        ]);
    }

    /**
     * Get dashboard by user
     *
     * @param User $user
     * @return string
     * @throws AuthorizationException
     */
    public static function getDashboardRouteForUser(User $user)
    {
        if ($user->isAdmin()) {
            return route('admin::orders.index') . '?sort=order_created';
        } elseif ($user->isStudent()) {
            return route('student::bookings.index');
        } elseif ($user->isOrganizationUser()) {
            return route('organization::courses.index') . '?calendar=true';
        } else {
            throw new AuthorizationException();
        }
    }

    /**
     * Get name for role
     *
     * @param $role
     * @return \Illuminate\Contracts\Translation\Translator|string
     */
    public static function getNameForRole($role)
    {
        $roles = static::getAvailableRoles();
        $role = $roles->search($role);

        return trans('roles.' . $role);
    }
}
