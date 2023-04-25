<?php

Route::get('/', ['uses' => 'IndexController@index', 'as' => 'shared::index.index']);
Route::post('/', ['uses' => 'IndexController@search', 'as' => 'shared::index.search']);

/**
 * OLD ROUTES
 */
Route::get('s/trafikskolor', function () {
    return redirect(route('shared::schools.index'), 301);
});

Route::get('s/{slug}/kurser/{courseType?}', function ($slug, $courseType = null) {
//    if ($courseType) {
//        return redirect(route('shared::'. $courseType, ['citySlug' => $slug]), 301);
//    }
    return redirect(route('shared::introduktionskurs', ['citySlug' => $slug]), 301);
});

Route::get('s/kurser/{courseType}', function ($courseType) {
    return redirect(route('shared::' . $courseType), 301);
});

//https://www.korkortsjakten.se/strangnas/trafikskolor/mickes-trafikskola-425
Route::get('{citySlug}/trafikskolor/{schoolSlug}', function ($citySlug, $schoolSlug) {
    return redirect(route('shared::schools.show', ['citySlug' => $citySlug, 'schoolSlug' => $schoolSlug]), 301);
});

/**
 * Courses redirection
 */
Route::get('kurser/{slug?}/{courseType?}', ['uses' => 'CourseController@course', 'as' => 'shared::search.courses']);
//Route::get('kurser/{citySlug}/{courseType?}', function ($citySlug, $courseType = null) {
//    if ($courseType) {
//        return redirect(route('shared::'. $courseType, ['citySlug' => $citySlug]), 301);
//    }
//    if (in_array($citySlug, ['introduktionskurs', 'riskettan', 'korlektion', 'mopedkurs am'])) {
//        return redirect(route('shared::' . $citySlug), 301);
//    }
//    return redirect(route('shared::introduktionskurs', ['citySlug' => $citySlug]), 301);
//})->name('shared::search.courses');

/**
 * Schools
 */
Route::group(['prefix' => 'trafikskolor'], function () {
    Route::get('/', ['uses' => 'SchoolController@index', 'as' => 'shared::schools.index']);
    Route::post('/rate/{schoolId}/{courseId}', ['uses' => 'SchoolController@rate', 'as' => 'shared::schools.rate']);
    Route::get('/rate/{schoolId}/{courseId}', ['uses' => 'SchoolController@rate', 'as' => 'shared::schools.rate']);

    /**
     * City
     */
    Route::group(['prefix' => '{citySlug}'], function () {
        Route::get('/', ['uses' => 'SchoolController@index', 'as' => 'shared::search.schools']);
        Route::group(['prefix' => '{schoolSlug}'], function () {
            Route::get('/', ['uses' => 'SchoolController@show', 'as' => 'shared::schools.show']);


            /**
             * Course
             */
            Route::group(['prefix' => 'kurser/{courseId}'], function () {
                Route::get('/', ['uses' => 'CourseController@show', 'as' => 'shared::courses.show']);
                Route::get('payment', ['uses' => 'CourseController@payment', 'as' => 'shared::courses.payment']);
                Route::get('confirmed', ['uses' => 'CourseController@confirmed', 'as' => 'shared::courses.confirmed'])->middleware('allowIframe');
            });
        });
    });
});

//this route is used for sharing our page in the schools' websites
Route::get('iframe-page/{school_id}', ['uses' => 'PageController@getIframePage','as' => 'shared::page.iframe'])->middleware('allowIframe');

Route::get('/introduktionskurser/{citySlug?}', ['uses' => 'CourseController@intro', 'as' => 'shared::introduktionskurs']);
Route::get('/riskettan/{citySlug?}', ['uses' => 'CourseController@riskettan', 'as' => 'shared::riskettan']);
Route::get('/paketerbjudande/{citySlug?}', ['uses' => 'CourseController@teorilektionPaket', 'as' => 'shared::teorilektion.paket']);
Route::get('/korlektion/{citySlug?}', ['uses' => 'CourseController@teorilektion', 'as' => 'shared::teorilektion']);
Route::get('/risktvaan/{citySlug?}', ['uses' => 'CourseController@risktvaan', 'as' => 'shared::risktvaan']);
//Route::get('/mopedkurs/{citySlug?}', ['uses' => 'CourseController@mopedkurs', 'as' => 'shared::mopedkurs']);
Route::get('/riskettanmc/{citySlug?}', ['uses' => 'CourseController@riskettanmc', 'as' => 'shared::riskettanmc']);
Route::get('/risktvaanmc/{citySlug?}', ['uses' => 'CourseController@risktvaanmc', 'as' => 'shared::risktvaanmc']);
Route::get('/engelskriskettan/{citySlug?}', ['uses' => 'CourseController@engelskriskettan', 'as' => 'shared::engelskriskettan']);
Route::get('/teoriprov-online', ['uses' => 'CourseController@teoriprovOnline', 'as' => 'shared::teoriprov-online']);
Route::get('/top-partner', ['uses' => 'PageController@topPartner', 'as' => 'shared::page.top-partner']);
Route::post('/application-top-partner', ['uses' => 'PageController@becomeTopPartner', 'as' => 'shared::page.application-top-partner']);
Route::get('/musikhjalpen', ['uses' => 'PageController@musicHelper', 'as' => 'shared::music-helper']);

Route::group(['namespace' => '\\Auth'], function () {
    // Login
    Route::get('/login', function () {
        return redirect()->route('auth::login');
    });
    Route::get('/logga-in', ['uses' => 'LoginController@showLoginForm', 'as' => 'auth::login']);
    Route::post('/logga-in', ['uses' => 'LoginController@login', 'as' => 'auth::login.do']);
    Route::get('/logga-ut', ['uses' => 'LoginController@logout', 'as' => 'auth::logout']);

    // Change password
    Route::get('losenord/skapa', ['uses' => 'PasswordController@create', 'as' => 'auth::password.create']);
    Route::post('losenord/skapa', ['uses' => 'PasswordController@store', 'as' => 'auth::password.store']);
    Route::get('losenord/glomt', ['uses' => 'ForgotPasswordController@show', 'as' => 'auth::password.forgot']);
    Route::post('losenord/aterstall', ['uses' => 'ForgotPasswordController@sendResetLinkEmail', 'as' => 'auth::password.reset']);
    Route::get('losenord/aterstall', ['uses' => 'ForgotPasswordController@reset', 'as' => 'auth::password.reset.do']);

    // Register
    Route::get('registrera', ['uses' => 'RegisterController@show', 'as' => 'auth::register.show']);
    Route::get('registrera/organisation', ['uses' => 'RegisterController@showOrganization', 'as' => 'auth::register.organization']);
    Route::post('registrera/organisation', ['uses' => 'RegisterController@storeOrganization', 'as' => 'auth::register.organization.store']);
    Route::get('registrera/organisation/finished', ['uses' => 'RegisterController@finishedOrganization', 'as' => 'auth::register.organization.finished']);
    Route::get('registrera/anvandare', ['uses' => 'RegisterController@showStudent', 'as' => 'auth::register.student']);
    Route::post('registrera/anvandare', ['uses' => 'RegisterController@storeStudent', 'as' => 'auth::register.student.store']);

    // Confirm registration
    Route::get('bekrafta', ['uses' => 'ConfirmationController@confirm', 'as' => 'auth::confirm']);
});

Route::get('kontakt/{subject?}/{school?}', ['uses' => 'PageController@contact', 'as' => 'shared::pages.contact']);

/**
 * OLD REDIRECT ROUTE
 */
Route::get('trafikskolor/{id}/{name}', function ($id) {
    $school = \Jakten\Models\School::with(['city'])->findOrFail($id);
    return redirect(route('shared::schools.show', ['citySlug' => $school->city->slug, 'schoolSlug' => $school->slug]), 301);
});

/**
 * Presentkort
 */
Route::get('presentkort', ['uses' => 'GiftCardController@index', 'as' => 'shared::gift_card.index']);
//Route::get('presentkort/confirmed/{klarnaOrderId}', ['uses' => 'GiftCardController@confirmed', 'as' => 'shared::gift_card.confirmed']);

Route::get('rapportera', function () {
    return redirect(route('shared::pages.contact', ['subject' => 'rapportera']), 301);
});

Route::get('betalningssida', ['uses' => 'PaymentController@index', 'as' => 'shared::payment.index'])->middleware('allowIframe');

/**
 * Payments
 */
Route::group(['prefix' => 'betalning'], function () {
    Route::group(['prefix' => 'bekrafta'], function () {
        Route::get('/{schoolSlug?}', ['uses' => 'PaymentController@confirmed', 'as' => 'shared::payment.confirmed'])->middleware('allowIframe');
    });
});


Route::group(['prefix' => 'presentkort'], function () {
    Route::get('aktivera/{token}', ['uses' => 'GiftCardController@claim', 'as' => 'shared::gift_card:claim']);
});

/**
 * Intensivkurser
 */
Route::get('paketerbjudande', function () {
    return view('shared::pages.intensivkurser');
});

Route::get('intensivkurser/{city}', ['uses' => 'PageController@intensiveCoursePage'])->where('city', '[A-Za-z]+');

//Route::get('träningskurser/{slug?}/{citySlug?}', ['uses' => 'CourseController@course', 'as' => 'shared::course']);

/**
 * Pages
 */
Route::get('{uri}', ['as' => 'shared::page.show', 'uses' => 'PageController@getPage'])->where('uri', '.*');

