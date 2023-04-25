<?php namespace Jakten\Helpers;

use Config;
use libphonenumber\{PhoneNumberFormat, PhoneNumberUtil};

/**
 * Wrapper for phone number normalisation.
 **/
class PhoneNumber
{
    /**
     * Validate phone numbers
     *
     * @return string
     **/
    public static function validate($number)
    {
        if (self::parsePhoneNumber($number)) {
            return true;
        }

        return false;
    }

    /**
     * @param $number
     * @return bool|string
     */
    public static function parsePhoneNumber($number)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $number = $phoneUtil->parse($number, self::parseConfigLocale());
        } catch (\libphonenumber\NumberParseException $e) {
            return false;
        }

        if ($phoneUtil->isValidNumber($number)) {
            return $phoneUtil->format($number, PhoneNumberFormat::INTERNATIONAL);
        }

        return false;
    }

    /**
     * Returns parsed config locale for google phone util.
     *
     * @return string
     **/
    protected static function parseConfigLocale()
    {
        $locale = Config::get('app.locale');

        if (strpos($locale, '-')) {
            $locale = explode('-', $locale);
        } elseif (strpos($locale, '_')) {
            $locale = explode('_', $locale);
        }

        return is_array($locale) ? $locale[1] : $locale;
    }
} // END class PhoneNumberHelper
