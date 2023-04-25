<?php namespace Jakten\Helpers;

use Jakten\Channels\{CustomMailChannel, CustomSMSChannel};

/**
 * Class Notify
 * @package Jakten\Helpers
 */
class NotifyChannels
{
    const TYPE_DASHBOARD = 1;
    const TYPE_EMAIL = 2;
    const TYPE_SMS = 3;

    /**
     * Notify types short names
     *
     * @var array
     */
    public static $types = [
        self::TYPE_DASHBOARD => 'dashboard',
        self::TYPE_EMAIL => 'email',
        self::TYPE_SMS => 'sms'
    ];


    /**
     * Notify types readable for humans
     *
     * @var array
     */
    public static $human = [
        self::TYPE_DASHBOARD => 'Dashboard',
        self::TYPE_EMAIL => 'E-mail',
        self::TYPE_SMS => 'SMS'
    ];

    /**
     * Channels for messages delivery
     *
     * @var array
     */
    public static $channels = [
        self::TYPE_DASHBOARD => 'database',
        self::TYPE_EMAIL => CustomMailChannel::class,
        self::TYPE_SMS => CustomSMSChannel::class
    ];
}
