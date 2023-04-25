<?php namespace Jakten\Providers;

use Illuminate\Support\{Facades\Hash, Facades\Validator, ServiceProvider};
use Jakten\Helpers\{InputParser, PhoneNumber};

/**
 * Class ValidationServiceProvider
 * @package Jakten\Providers
 */
class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any validation rules.
     *
     * @return void
     */
    public function boot()
    {
        // Validate that a phone number is valid
        Validator::extend('phone_number', function ($attribute, $value, $parameters, $validator) {
            return PhoneNumber::validate(str_replace(' ', '', $value));
        });

        Validator::extend('zip', function ($attribute, $value, $parameters, $validator) {
            $value = preg_replace('/\D/', '', $value);
            if (strlen($value) === 5) {
                return true;
            }

            return false;
        });

        Validator::extend('org_number', function ($attribute, $value, $parameters, $validator) {
            $value = InputParser::orgNumber($value);

            if (!preg_match('/^\d{10}+$/', $value)) {
                return false;
            }

            if (config('app.env') === 'local' || config('app.env') === 'test') {
                return true;
            }

            // check if it really is a valid orgnumber
            $nn = '';
            for ($n = 0; $n < strlen($value); $n++) {
                $nn .= (((($n + 1) % 2) + 1) * substr($value, $n, 1));
            }

            $checksum = 0;

            for ($n = 0; $n < strlen($nn); $n++) {
                $checksum += substr($nn, $n, 1) * 1;
            }

            return $checksum % 10 == 0;
        });

        Validator::extend('password_match', function ($attribute, $value, $parameters, $validator) {
            $currentPassword = end($parameters);

            return Hash::check($value, $currentPassword);
        });

        Validator::extend('website', function ($attribute, $value, $parameters, $validator) {
            if (filter_var($value, FILTER_VALIDATE_URL) === false) {
                return false;
            } else {
                return true;
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
