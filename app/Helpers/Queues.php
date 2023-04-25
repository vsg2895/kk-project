<?php namespace Jakten\Helpers;

/**
 * Class Queues
 * @package Jakten\Helpers
 */
class Queues
{
    /**
     * @constants
     */
    const PREFIX = 'queue';
    const TYPE_EMAIL = 'email';
    const TYPE_SMS = 'sms';

    /**
     * Returns the queue name prefixed with the current environment
     *
     * @param $type
     *
     * @return string
     */
    public static function getName($type)
    {
        return self::PREFIX . '-' . config('app.env') . '-' . $type;
    }
}
