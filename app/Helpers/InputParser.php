<?php namespace Jakten\Helpers;

/**
 * Class InputParser
 * @package Jakten\Helpers
 */
class InputParser
{
    /**
     * @param $number
     * @return bool|null|string|string[]
     */
    public static function orgNumber($number)
    {
        $number = preg_replace('/\D/', '', $number);
        if (strlen($number) == 12) {
            $number = substr($number, 2);
        }

        if (!preg_match('/^\d{10}+$/', $number)) {
            return false;
        }

        return $number;
    }
}
