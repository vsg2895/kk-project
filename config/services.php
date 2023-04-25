<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => \Jakten\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'phones' => [
        'customers' => [
            'text' => '010 58 58 590',
            'regular' => '0105858590'
        ]
    ],

    'rollbar' => [
        'access_token' => env('ROLLBAR_TOKEN'),
        'max_times' => 10,
        'environment' => env('APP_ENV')
    ],

    'rule' => [
        'url' => env('RULE_URL'),
        'token' => env('RULE_API_TOKEN'),
    ],

];
