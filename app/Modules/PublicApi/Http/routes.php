<?php

/**********
* Klarna
*********/
Route::group(['prefix' => 'klarna', 'laroute' => false], function () {
    Route::group(['prefix' => 'onboarding'], function () {
        Route::post('{organizationId}', ['uses' => 'KlarnaController@onboardingResponse', 'as' => 'public::klarna.onboarding']);
        Route::post('{organizationId}/update', ['uses' => 'KlarnaController@onboardingUpdate', 'as' => 'public::klarna.onboarding.update']);
    });
    Route::group(['prefix' => 'orders'], function () {});

    Route::group(['prefix' => 'checkout'], function () {
        Route::group(['prefix' => 'push'], function () {
            Route::post('{schoolId?}', ['uses' => 'KlarnaController@push', 'as' => 'public::klarna.checkout.push']);
        });
        //Route::get('show/{schoolId?}', ['uses' => 'KlarnaController@show', 'as' => 'public::klarna.checkout.show']);
    });
});
