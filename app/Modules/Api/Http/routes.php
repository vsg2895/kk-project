<?php

/**
 * Auth
 */
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', ['uses' => 'AuthController@login', 'as' => 'api::auth.login']);
    Route::get('user', ['uses' => 'AuthController@getUser', 'as' => 'api::auth.user', 'middleware' => 'auth']);
});

/**
 * statistics
 */
Route::group(['prefix' => 'statistics'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/orders/{startDate}/{endDate}/{granularity}/{type}/{cityId}/{orgId}/{schoolId}', ['uses' => 'StatisticsController@orders', 'as' => 'api::statistics.orders']);
        Route::get('/organization/{organization}/{granularity}/{type}/{startDate}/{endDate}/{id?}', ['uses' => 'StatisticsController@getOrganization', 'as' => 'api::statistics.organization']);
        Route::get('/topFive/{id?}', ['uses' => 'StatisticsController@topFive', 'as' => 'api::statistics.topFive']);
        Route::get('/orders/{granularity?}/{type?}/{id?}', ['uses' => 'StatisticsController@getOrders', 'as' => 'api::statistics.orders']);
        Route::get('/courses/{granularity?}/{type?}/{id?}', ['uses' => 'StatisticsController@getCourses', 'as' => 'api::statistics.courses']);
        Route::get('/cancellations/{granularity?}/{type?}/{id?}', ['uses' => 'StatisticsController@getCancellations', 'as' => 'api::statistics.cancellations']);
        Route::get('/contact/{granularity?}/{type?}/{id?}', ['uses' => 'StatisticsController@getContact', 'as' => 'api::statistics.contact']);
        Route::get('/schools/{connectedIn?}', ['uses' => 'StatisticsController@getSchools', 'as' => 'api::statistics.schools']);
        Route::get('/users', ['uses' => 'StatisticsController@getUsers', 'as' => 'api::statistics.users']);
    });
});

/*
 * Orders
 */
Route::group(['prefix' => 'orders'], function () {
    Route::post('course/store/{courseId}', ['uses' => 'OrderController@storeOrder', 'as' => 'api::orders.store']);

    Route::group(['middleware' => 'auth'], function () {
        Route::post('{id}/cancel', ['uses' => 'OrderController@cancel', 'as' => 'api::orders.cancel']);
        Route::get('{id}', ['uses' => 'OrderController@find', 'as' => 'api::orders.find']);
        Route::post('{id}', ['uses' => 'OrderController@update', 'as' => 'api::orders.update']);
        Route::post('{id}/klarna', ['uses' => 'OrderController@activateOrCancel', 'as' => 'api::orders.activate_or_cancel', 'laroute' => true]);
    });
});

/****
 * Invoices
 */
Route::group(['prefix' => 'invoices', 'middleware' => 'auth.admin'], function () {
    Route::get('/', ['uses' => 'InvoiceController@getAll', 'as' => 'api::invoices.all']);
    Route::post('/store', ['uses' => 'InvoiceController@store', 'as' => 'api::invoices.store']);
    Route::post('/{invoiceId}/sent', ['uses' => 'InvoiceController@sent', 'as' => 'api::invoices.sent']);
    Route::post('/{invoiceId}/paid', ['uses' => 'InvoiceController@paid', 'as' => 'api::invoices.paid']);
});

/*
 * Cities
 */
Route::group(['prefix' => 'cities'], function () {
    Route::get('/', ['uses' => 'CityController@getAll', 'as' => 'api::cities.all']);
});

/*
 * Counties
 */
Route::group(['prefix' => 'counties'], function () {
    Route::get('/', ['uses' => 'CountyController@getAll', 'as' => 'api::counties.all']);
});

/*
 * Courses
 */
Route::group(['prefix' => 'courses'], function () {
    Route::get('/search', ['uses' => 'CourseController@search', 'as' => 'api::courses.search']);
    Route::get('{id}', ['uses' => 'CourseController@find', 'as' => 'api::courses.find']);
    Route::post('{id}', ['uses' => 'CourseController@update', 'as' => 'api::courses.update']);
});

/*
 * Schools
 */
Route::group(['prefix' => 'schools'], function () {
    Route::get('/', ['uses' => 'SchoolController@getAll', 'as' => 'api::schools.all']);
    Route::get('/user', ['uses' => 'SchoolController@getForLoggedInUser', 'as' => 'api::schools.user']);
    Route::get('/search', ['uses' => 'SchoolController@search', 'as' => 'api::schools.search']);
    Route::post('/store', ['uses' => 'SchoolController@store', 'as' => 'api::schools.store', 'middleware' => 'auth']);
    Route::get('{id}', ['uses' => 'SchoolController@find', 'as' => 'api::schools.find']);
    Route::post('{id}/claim', ['uses' => 'SchoolController@claim', 'as' => 'api::schools.claim']);
    Route::get('acceptsGiftCard/{cityId}', ['uses' => 'SchoolController@getGiftCardSchools', 'as' => 'api::schools.acceptsGiftCard']);
    Route::get('get-loyalty-level/{id}', ['uses' => 'SchoolController@getSchoolLoyaltyLevel', 'as' => 'api::schools.getLoyaltyLevel']);

    Route::post('/saveOrder', ['uses' => 'SchoolController@saveOrder', 'as' => 'api::schools.save.order', 'middleware' => 'auth']);
    Route::post('/{id}/connected', ['uses' => 'SchoolController@updateConnectedStatus', 'as' => 'api::schools.update.connected']);

    /*
     * Rating
     */
    Route::get('/{id}/user-rating', ['uses' => 'RatingController@findByUser', 'as' => 'api::schools.user_rating']);

    Route::group(['middleware' => 'auth.student'], function () {
        Route::post('/{id}/rate', ['uses' => 'RatingController@rate', 'as' => 'api::schools.rate']);
        Route::delete('{id}/rate', ['uses' => 'RatingController@delete', 'as' => 'api::schools.delete_rate']);
    });

    Route::get('/{id}/rate-course/{course}', ['uses' => 'RatingController@rateCourse', 'as' => 'api::schools.rate_course']);

});

/*
 * Vehicles
 */
Route::group(['prefix' => 'vehicles'], function () {
    Route::get('/', ['uses' => 'VehicleController@getAll', 'as' => 'api::vehicles.all']);
    Route::get('/{id}', ['uses' => 'VehicleController@getForSchool', 'as' => 'api::vehicles.school']);
    Route::get('/order/{id}', ['uses' => 'VehicleController@getOrderForSchool', 'as' => 'api::vehicles.school.order']);
});

/*
 * Prices
 */
Route::group(['prefix' => 'segments'], function () {
    Route::get('/', ['uses' => 'VehicleSegmentController@getAll', 'as' => 'api::vehicle_segments.all']);
    Route::get('/school/{id}', ['uses' => 'VehicleSegmentController@getForSchool', 'as' => 'api::vehicle_segments.school']);
});

Route::group(['prefix' => 'klarna'], function () {
    Route::post('{courseId}', ['uses' => 'KlarnaController@store', 'as' => 'api::klarna.store']);
    Route::get('{id}', ['uses' => 'KlarnaController@find', 'as' => 'api::klarna.find']);
    Route::post('update/{orderId}', ['uses' => 'KlarnaController@update', 'as' => 'api::klarna.update']);
    Route::post('onboarding/initiate', ['uses' => 'KlarnaController@initiateOnboarding', 'as' => 'api::klarna.onboarding.initiate', 'middleware' => 'auth.organization']);
});

Route::group(['prefix' => 'gift-card'], function () {
    Route::post('check', [
        'uses' => 'GiftCardController@check',
        'as' => 'api::giftcard.check']);

    Route::group(['prefix' => 'type'], function () {
        Route::get('/', [
            'uses' => 'GiftCardTypeController@index',
            'as' => 'api::giftcard.type.index']);

        Route::get('/{id}', [
            'uses' => 'GiftCardTypeController@show',
            'as' => 'api::giftcard.type.show']);
    });

    Route::get('{id}', ['uses' => 'KlarnaController@find', 'as' => 'api::klarna.find']);
});

Route::post('contact', ['uses' => 'ContactController@storeContact', 'as' => 'api::contact.store']);

/*
 * Blog
 */
Route::group(['prefix' => 'blog'], function () {
    Route::group(['prefix' => 'posts'], function () {
        Route::get('/search', ['uses' => 'BlogController@searchPosts', 'as' => 'api::blog.posts.search']);
        Route::get('/{post}', ['uses' => 'BlogController@findPost', 'as' => 'api::blog.posts.find']);
    });

    Route::group(['prefix' => 'comments'], function () {
        Route::get('/search', ['uses' => 'BlogController@searchComments', 'as' => 'api::blog.comments.search']);
    });
});
