<?php

Route::get('/', [
    'uses' => 'DashboardController@dashboard',
    'as' => 'organization::dashboard',
    'laroute' => true
]);

Route::group([], function () {
    Route::get('information', ['uses' => 'OrganizationController@show', 'as' => 'organization::organization.show', 'laroute' => true]);
    Route::post('information', ['uses' => 'OrganizationController@update', 'as' => 'organization::organization.update']);
});

/*
 * Orders
 */
Route::group(['prefix' => 'orders'], function () {
    Route::get('/', ['uses' => 'OrderController@index', 'as' => 'organization::orders.index']);
    Route::get('/{id}', ['uses' => 'OrderController@show', 'as' => 'organization::orders.show']);
    Route::post('/{id}', ['uses' => 'OrderController@update', 'as' => 'organization::orders.update']);
    Route::post('/{id}/cancel', ['uses' => 'OrderController@cancel', 'as' => 'organization::orders.cancel']);
});

/***
 * Courses
 */
Route::group(['prefix' => 'courses'], function () {
    Route::get('/', ['uses' => 'CourseController@index', 'as' => 'organization::courses.index']);
    Route::get('/create', ['uses' => 'CourseController@create', 'as' => 'organization::courses.create']);
    Route::post('/store', ['uses' => 'CourseController@store', 'as' => 'organization::courses.store']);
    Route::get('{id}', ['uses' => 'CourseController@show', 'as' => 'organization::courses.show']);
    Route::get('/download/{id}', ['uses' => 'CourseController@downloadParticipants', 'as' => 'organization::courses.download.participants']);
    Route::post('{id}', ['uses' => 'CourseController@update', 'as' => 'organization::courses.update']);
    Route::delete('{id}/delete', ['uses' => 'CourseController@delete', 'as' => 'organization::courses.delete']);
});

/*
 * School information
 */
Route::group(['prefix' => 'schools'], function () {
    Route::get('/', ['uses' => 'SchoolController@index', 'as' => 'organization::schools.index', 'laroute' => true]);
    Route::get('/create', ['uses' => 'SchoolController@create', 'as' => 'organization::schools.create']);
    Route::post('/create', ['uses' => 'SchoolController@store', 'as' => 'organization::schools.store']);
    Route::get('/{id}', ['uses' => 'SchoolController@show', 'as' => 'organization::schools.show']);
    Route::post('/{id}/details', ['uses' => 'SchoolController@updateDetails', 'as' => 'organization::schools.update.details']);
    Route::post('/{id}/prices', ['uses' => 'SchoolController@updatePrices', 'as' => 'organization::schools.update.prices']);
    Route::post('/{id}/add-custom-addon', ['uses' => 'SchoolController@addCustomAddon', 'as' => 'organization::schools.create.customAddon']);
    Route::get('/{id}/remove-custom-addon', ['uses' => 'SchoolController@removeCustomAddon', 'as' => 'organization::schools.delete.customAddon']);
});

//School images for gallery
Route::group(['prefix' => 'schools/images', 'as' => 'organization::schools.images'], function () {
    Route::post('/{school}', ['uses' => 'SchoolGalleryController@store', 'as' => '.store']);
    Route::get('/{schoolImage}', ['uses' => 'SchoolGalleryController@delete', 'as' => '.delete']);
});

Route::group(['prefix' => 'ratings'], function () {
    Route::get('/', ['uses' => 'RatingController@index', 'as' => 'organization::ratings.index']);
});

Route::group(['prefix' => 'usps'], function () {
    Route::get('/create', ['uses' => 'UspsController@create', 'as' => 'organization::usps.create']);
    Route::post('/create', ['uses' => 'UspsController@store', 'as' => 'organization::usps.store']);
    Route::get('{usps}', ['uses' => 'UspsController@show', 'as' => 'organization::usps.show']);
    Route::post('{usps}', ['uses' => 'UspsController@update', 'as' => 'organization::usps.update']);
    Route::delete('{id}/delete', ['uses' => 'UspsController@delete', 'as' => 'organization::usps.delete']);
});

/*
 * User
 */
Route::get('users', ['uses' => 'UserController@index', 'as' => 'organization::users.index']);
Route::get('users/create', ['uses' => 'UserController@create', 'as' => 'organization::users.create']);
Route::post('users/store', ['uses' => 'UserController@store', 'as' => 'organization::users.store']);

Route::get('users/back', ['uses' => 'UserController@back', 'as' => 'organization::user.login.back']);

Route::group(['prefix' => 'profile'], function () {
    Route::get('/{id?}', ['uses' => 'UserController@show', 'as' => 'organization::user.show']);
    Route::post('/{id?}', ['uses' => 'UserController@update', 'as' => 'organization::user.update']);
});

Route::group(['prefix' => 'notify'], function () {

    $shared = '\\' . $this->sharedNamespace . '\NotifyController';

    /**
     * Notifications settings
     */
    Route::get('/', ['uses' => $shared . '@index', 'as' => 'organization::notify.index']);
    Route::post('/', ['uses' => $shared . '@update', 'as' => 'organization::notify.update']);

    /**
     * Notifications messages
     */
    Route::get('messages',  ['uses' => $shared . '@messages', 'as' => 'organization::notify.messages']);
    Route::post('messages', ['uses' => $shared . '@mark', 'as' => 'organization::notify.mark']);
});

Route::get('/test-native', ['uses' => 'DashboardController@testNative']);

Route::group(['prefix' => 'statistics'], function () {
    Route::get('/{school_id?}', ['uses' => 'StatisticsController@index', 'as' => 'organization::statistics.index']);
});
