<?php

return [
    'booking_fee_to_kkj_name' => 'Bokningsavgift',
    'booking_fee_to_kkj' => env('BOOKING_FEE_TO_KKJ', 39),
    'provision' => env('DEFAULT_PROVISION_TO_KKJ', 12),
    'courses' => env('DEFAULT_PROVISION_COURSES', 10),
    'packages' => env('DEFAULT_PROVISION_PACKAGE', 5),
    'klarna' => env('DEFAULT_PROVISION_KLARNA', 1.8),
    'moms' => env('DEFAULT_PROVISION_MOMS', 25),
    'moms_inv' => env('DEFAULT_PROVISION_MOMS_INVOICE', 20),
    'moms_edu' => env('DEFAULT_PROVISION_MOMS_EDU', 12),
    'moms_edu_inv' => env('DEFAULT_PROVISION_MOMS_EDU', 12),

    'cancel_courses_allowed' => env('CANCEL_COURSES_ALLOWED', 14),
    'cancel_packages_allowed' => env('CANCEL_PACKAGES_ALLOWED', 2),
];
