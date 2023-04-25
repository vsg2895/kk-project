<?php namespace Jakten\Helpers;

/**
 * Class KlarnaSignup
 * @package Jakten\Helpers
 */
class KlarnaSignup
{
    const STATUS_NOT_INITIATED = 'NOT_INITIATED';
    const STATUS_WAITING = 'WAITING';
    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_ACCESSED = 'ACCESSED';
    const STATUS_SUBMITTED = 'SUBMITTED';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_COMPLETED = 'COMPLETED';

    /**
     * @param $status
     * @return string
     */
    public static function textForStatus($status)
    {
        if ($status === static::STATUS_NOT_INITIATED) {
            return 'För att ansluta dig med Klarna...';
        } elseif ($status === static::STATUS_WAITING) {
            return 'Väntar på godkännande från Klarna, processen kan ta mellan 1-2 arbetsdagar.';
        } elseif ($status === static::STATUS_REJECTED) {
            return 'Tyvärr har Klarna valt att inte godkänna er trafikskola.';
        } elseif ($status === static::STATUS_SUCCESS || $status === static::STATUS_ACCESSED || $status === static::STATUS_CANCELLED) {
            return 'Klarna har godkänt dig, nu måste du bara besvara kontrollfrågorna som Klarna mailat till dig.';
        } elseif ($status === static::STATUS_SUBMITTED) {
            return 'Klarna har fått dina svar på kontrollfrågorna, snart kommer du ha tillgång till alla Klarnas tjänster.';
        } elseif ($status === static::STATUS_COMPLETED) {
            return 'Du har blivit godkänd av Klarna!';
        }
    }
}
