<?php

Route::get('/', [
    'uses' => 'DashboardController@dashboard',
    'as' => 'student::dashboard',
]);

/*
 * Bookings
 */
Route::group(['prefix' => 'bookings'], function () {
    Route::get('/', ['uses' => 'BookingController@index', 'as' => 'student::bookings.index']);
    Route::get('/{id}', ['uses' => 'BookingController@show', 'as' => 'student::bookings.show']);
});

/*
 * Ratings
 */
Route::group(['prefix' => 'ratings'], function () {
    Route::get('/', ['uses' => 'RatingController@index', 'as' => 'student::ratings.index']);
    Route::get('/{id}', ['uses' => 'RatingController@show', 'as' => 'student::ratings.show']);
    Route::post('/{id}', ['uses' => 'RatingController@update', 'as' => 'student::ratings.update']);
    Route::delete('/{id}/delete', ['uses' => 'RatingController@delete', 'as' => 'student::ratings.delete']);
});

/*
 * User
 */
Route::group(['prefix' => 'profile'], function () {
    Route::get('/', ['uses' => 'UserController@show', 'as' => 'student::user.show']);
    Route::post('/', ['uses' => 'UserController@update', 'as' => 'student::user.update']);
    Route::post('/delete', ['uses' => 'UserController@delete', 'as' => 'student::user.delete']);
});

Route::group(['prefix' => 'orders'], function () {
    Route::get('/', ['uses' => 'OrderController@index', 'as' => 'student::orders.index']);
    Route::get('/{id}', ['uses' => 'OrderController@show', 'as' => 'student::orders.show']);
    Route::post('/{id}/cancel', ['uses' => 'OrderController@cancel', 'as' => 'student::orders.cancel']);
    Route::post('/{id}/rebook', ['uses' => 'OrderController@rebook', 'as' => 'student::orders.rebook']);
});

Route::group(['prefix' => 'gift-cards'], function () {
    Route::get('/', ['uses' => 'GiftCardController@index', 'as' => 'student::giftcard.index']);
    Route::post('/claim', ['uses' => 'GiftCardController@claim', 'as' => 'student::giftcard.claim']);
});

Route::group(['prefix' => 'notify'], function () {

    $shared = '\\' . $this->sharedNamespace . '\NotifyController';

    /**
     * Notifications settings
     */
    Route::get('/', ['uses' => $shared . '@index', 'as' => 'student::notify.index']);
    Route::post('/', ['uses' => $shared . '@update', 'as' => 'student::notify.update']);

    /**
     * Notifications messages
     */
    Route::get('messages',  ['uses' => $shared . '@messages', 'as' => 'student::notify.messages']);
    Route::post('messages', ['uses' => $shared . '@mark', 'as' => 'student::notify.mark']);
});
