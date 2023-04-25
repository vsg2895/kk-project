<?php

return [
    'base_url' => env('KLARNA_BASE_URL', 'https://checkout.testdrive.klarna.com'),
    'kkj_payment_id' => env('KLARNA_KKJ_PAYMENT_ID', ''),
    'kkj_payment_secret' => env('KLARNA_KKJ_PAYMENT_SECRET', ''),

    'kkj_native_onboarding_url' => env('KLARNA_NATIVE_ONBOARDING_URL', ''),
    'kkj_native_onboarding_username' => env('KLARNA_NATIVE_ONBOARDING_USERNAME', ''),
    'kkj_native_onboarding_payment_secret' => env('KLARNA_NATIVE_ONBOARDING_PASSWORD', ''),

    'rebooking' => 'Bonus Saldo',
    'gift_cart_name' => 'Presentkort',
    'promo_cart_name' => 'Rabattkod',
    'theory_discount' => 'Körkortsteori och Testprov',
];
