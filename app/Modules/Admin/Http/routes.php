<?php
// Dashboard page: /admin
use Illuminate\Support\Facades\Route;

Route::get('/', ['uses' => 'DashboardController@dashboard', 'as' => 'admin::dashboard']);

// Laravel Log Viewer (hidden url)
Route::get('/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

// Orders urls group
Route::group(['prefix' => 'orders'], function () {
    Route::get('/', ['uses' => 'OrderController@index', 'as' => 'admin::orders.index']);
    Route::post('invoice-sent', ['uses' => 'OrderController@invoiceSentMany', 'as' => 'admin::orders.invoice_sent_many']);
    Route::post('delete-order-item', ['uses' => 'OrderController@deleteOrderDetails', 'as' => 'admin::orders.order_details.delete']);
    Route::post('{id}/cancel', ['uses' => 'OrderController@cancel', 'as' => 'admin::admin::orders.cancel']);
    Route::post('{id}/rebook', ['uses' => 'OrderController@rebook', 'as' => 'admin::admin::orders.rebook']);
    Route::get('{id}', ['uses' => 'OrderController@show', 'as' => 'admin::orders.show']);
    Route::get('{id}/edit', ['uses' => 'OrderController@edit', 'as' => 'admin::orders.edit']);
    Route::post('{id}/update', ['uses' => 'OrderController@update', 'as' => 'admin::orders.update']);
    Route::post('{id}', ['uses' => 'OrderController@transmission', 'as' => 'admin::orders.transmission']);
    Route::post('{id}/invoice-sent', ['uses' => 'OrderController@invoiceSent', 'as' => 'admin::orders.invoice_sent']);
});

// Search algorithm urls group
Route::group(['prefix' => 'search-algorithm'], function () {
    Route::get('/', ['uses' => 'SearchAlgorithmController@index', 'as' => 'admin::search_algorithm.index']);
    Route::post('/', ['uses' => 'SearchAlgorithmController@update', 'as' => 'admin::search_algorithm.update']);
});

// Gift cards urls group
Route::group(['prefix' => 'gift-cards'], function () {
    Route::get('/', ['uses' => 'GiftCardController@index', 'as' => 'admin::gift_cards.index']);
    Route::post('/', ['uses' => 'GiftCardController@update', 'as' => 'admin::gift_cards.update']);
});

// Reports page (hidden url)
Route::group(['prefix' => 'reports'], function () {
    Route::get('/', ['uses' => 'ReportsController@index', 'as' => 'admin::reports.index']);
    Route::match(['get', 'post'], '/orders', ['uses' => 'ReportsController@orders', 'as' => 'admin::reports.orders']);
    Route::match(['get', 'post'], '/schools', ['uses' => 'ReportsController@schools', 'as' => 'admin::reports.schools']);
    Route::get('/download-orders', ['uses' => 'ReportsController@downloadOrders', 'as' => 'admin::reports.download.orders']);
    Route::get('/download-invoice', ['uses' => 'ReportsController@downloadInvoice', 'as' => 'admin::reports.download.invoice']);
    Route::get('/students', ['uses' => 'ReportsController@students', 'as' => 'admin::reports.students']);
    Route::get('/students/export', ['uses' => 'ReportsController@studentsExport', 'as' => 'admin::reports.students.export']);
});

// Courses urls group
Route::group(['prefix' => 'courses'], function () {
    Route::get('/', ['uses' => 'CourseController@index', 'as' => 'admin::courses.index']);
    Route::get('/create', ['uses' => 'CourseController@create', 'as' => 'admin::courses.create']);
    Route::post('/create', ['uses' => 'CourseController@store', 'as' => 'admin::courses.store']);
    Route::get('/order', ['uses' => 'CourseController@order', 'as' => 'admin::courses.order']);
    Route::post('/order', ['uses' => 'CourseController@orderStore', 'as' => 'admin::courses.order.store']);
    Route::post('{id}', ['uses' => 'CourseController@update', 'as' => 'admin::courses.update']);
    Route::get('{id}', ['uses' => 'CourseController@show', 'as' => 'admin::courses.show']);
    Route::get('/download/{id}', ['uses' => 'CourseController@downloadParticipants', 'as' => 'admin::courses.download.participants']);
    Route::delete('{id}/delete', ['uses' => 'CourseController@delete', 'as' => 'admin::courses.delete']);
});

// Schools urls group
Route::group(['prefix' => 'schools'], function () {
    Route::get('/', ['uses' => 'SchoolController@index', 'as' => 'admin::schools.index']);
    Route::get('/create', ['uses' => 'SchoolController@create', 'as' => 'admin::schools.create']);
    Route::post('/create', ['uses' => 'SchoolController@store', 'as' => 'admin::schools.store']);
    Route::get('{id}', ['uses' => 'SchoolController@show', 'as' => 'admin::schools.show']);
    Route::post('{id}/details', ['uses' => 'SchoolController@updateDetails', 'as' => 'admin::schools.update.details']);
    Route::post('{id}/prices', ['uses' => 'SchoolController@updatePrices', 'as' => 'admin::schools.update.prices']);
    Route::post('{id}/comment', ['uses' => 'SchoolController@createComment', 'as' => 'admin::schools.create.comment']);
    Route::delete('{id}/delete', ['uses' => 'SchoolController@delete', 'as' => 'admin::schools.delete']);
    Route::post('/{id}/add-custom-addon', ['uses' => 'SchoolController@addCustomAddon', 'as' => 'admin::schools.create.customAddon']);
    Route::get('/{id}/remove-custom-addon', ['uses' => 'SchoolController@removeCustomAddon', 'as' => 'admin::schools.delete.customAddon']);
    Route::get('/list/export', ['uses' => 'SchoolController@exportSchoolsList', 'as' => 'admin::schools.list.export']);

    Route::post('/{id}/fees', ['uses' => 'SchoolController@updateFees', 'as' => 'admin::schools.update.fees']);
});

//School images for gallery
Route::group(['prefix' => 'schools/images', 'as' => 'admin::schools.images'], function () {
    Route::post('/{school}', ['uses' => 'SchoolGalleryController@store', 'as' => '.store']);
    Route::get('/{schoolImage}', ['uses' => 'SchoolGalleryController@delete', 'as' => '.delete']);
});

// Usps urls group
Route::group(['prefix' => 'usps'], function () {
    Route::get('/create', ['uses' => 'UspsController@create', 'as' => 'admin::usps.create']);
    Route::post('/create', ['uses' => 'UspsController@store', 'as' => 'admin::usps.store']);
    Route::get('{usps}', ['uses' => 'UspsController@show', 'as' => 'admin::usps.show']);
    Route::post('{usps}', ['uses' => 'UspsController@update', 'as' => 'admin::usps.update']);
    Route::delete('{id}/delete', ['uses' => 'UspsController@delete', 'as' => 'admin::usps.delete']);
});

// Organizations urls group
Route::group(['prefix' => 'organizations'], function () {
    Route::get('/', ['uses' => 'OrganizationController@index', 'as' => 'admin::organizations.index']);
    Route::get('/create', ['uses' => 'OrganizationController@create', 'as' => 'admin::organizations.create']);
    Route::post('/create', ['uses' => 'OrganizationController@store', 'as' => 'admin::organizations.store']);
    Route::get('/{id}', ['uses' => 'OrganizationController@show', 'as' => 'admin::organizations.show']);
    Route::post('/{id}', ['uses' => 'OrganizationController@update', 'as' => 'admin::organizations.update']);
    Route::delete('{id}/delete', ['uses' => 'OrganizationController@delete', 'as' => 'admin::organizations.delete']);
});

// Cities urls group
Route::group(['prefix' => 'cities'], function () {
    Route::get('/', ['uses' => 'CityController@index', 'as' => 'admin::cities.index']);
    Route::get('/{id}', ['uses' => 'CityController@show', 'as' => 'admin::cities.show']);
    Route::post('/{id}', ['uses' => 'CityController@update', 'as' => 'admin::cities.update']);
});


// Users urls group
Route::group(['prefix' => 'users'], function () {
    Route::get('/', ['uses' => 'UserController@index', 'as' => 'admin::users.index']);
    Route::get('/create', ['uses' => 'UserController@create', 'as' => 'admin::users.create']);
    Route::post('/create', ['uses' => 'UserController@store', 'as' => 'admin::users.store']);
    Route::get('{id}', ['uses' => 'UserController@show', 'as' => 'admin::users.show']);
    Route::post('{id}', ['uses' => 'UserController@update', 'as' => 'admin::users.update']);
    Route::delete('{id}/delete', ['uses' => 'UserController@delete', 'as' => 'admin::users.delete']);
    Route::post('{id}/restore', ['uses' => 'UserController@restore', 'as' => 'admin::users.restore']);
});

// Invoices urls group
Route::group(['prefix' => 'invoices'], function () {
    Route::get('/', ['uses' => 'InvoiceController@index', 'as' => 'admin::invoices.index']);
    Route::get('create/{orderId?}', ['uses' => 'InvoiceController@create', 'as' => 'admin::invoices.create']);
    Route::post('store', ['uses' => 'InvoiceController@store', 'as' => 'admin::invoices.store']);
    Route::get('{id}', ['uses' => 'InvoiceController@show', 'as' => 'admin::invoices.show', 'laroute' => true]);
});

// Pages urls group
Route::group(['prefix' => 'pages'], function () {
    Route::get('/', ['uses' => 'PageController@index', 'as' => 'admin::pages.index']);
    Route::get('/create', ['uses' => 'PageController@create', 'as' => 'admin::pages.create']);
    Route::post('/create', ['uses' => 'PageController@store', 'as' => 'admin::pages.store']);
    Route::get('{id}', ['uses' => 'PageController@show', 'as' => 'admin::pages.show']);
    Route::post('{id}', ['uses' => 'PageController@update', 'as' => 'admin::pages.update']);
    Route::delete('{id}/delete', ['uses' => 'PageController@delete', 'as' => 'admin::pages.delete']);
});

// Contact urls group
Route::group(['prefix' => 'contact'], function () {
    Route::get('/', ['uses' => 'ContactRequestController@index', 'as' => 'admin::contact_request.index']);
    Route::get('{id}', ['uses' => 'ContactRequestController@show', 'as' => 'admin::contact_request.show']);
});

// Ratings urls group
Route::group(['prefix' => 'ratings'], function () {
    Route::get('/', ['uses' => 'RatingController@index', 'as' => 'admin::ratings.index']);
    Route::get('/create', ['uses' => 'RatingController@create', 'as' => 'admin::ratings.create']);
    Route::post('/store', ['uses' => 'RatingController@store', 'as' => 'admin::ratings.store']);
    Route::get('/edit/{rating}', ['uses' => 'RatingController@edit', 'as' => 'admin::ratings.edit']);
    Route::post('/update/{rating}', ['uses' => 'RatingController@update', 'as' => 'admin::ratings.update']);
    Route::get('/delete/{rating}', ['uses' => 'RatingController@delete', 'as' => 'admin::ratings.delete']);
});

Route::group(['prefix' => 'partners'], function () {
    Route::get('/', ['uses' => 'PartnerController@index', 'as' => 'admin::partners.index']);
    Route::get('/create', ['uses' => 'PartnerController@create', 'as' => 'admin::partners.create']);
    Route::post('/create', ['uses' => 'PartnerController@store', 'as' => 'admin::partners.store']);
    Route::get('/{partner}/delete', ['uses' => 'PartnerController@destroy', 'as' => 'admin::partners.destroy']);
    Route::get('/{partner}/edit', ['uses' => 'PartnerController@edit', 'as' => 'admin::partners.edit']);
    Route::post('/{partner}/edit', ['uses' => 'PartnerController@update', 'as' => 'admin::partners.update']);
});

Route::group(['prefix' => 'course_type'], function () {
    Route::get('/', ['uses' => 'CourseTypeController@index', 'as' => 'admin::course_type.index']);
    Route::get('/create', ['uses' => 'CourseTypeController@create', 'as' => 'admin::course_type.create']);
    Route::post('/create', ['uses' => 'CourseTypeController@store', 'as' => 'admin::course_type.store']);
    Route::get('/{segment}/delete', ['uses' => 'CourseTypeController@destroy', 'as' => 'admin::course_type.destroy']);
    Route::get('/{segment}/edit', ['uses' => 'CourseTypeController@edit', 'as' => 'admin::course_type.edit']);
    Route::post('/{segment}/edit', ['uses' => 'CourseTypeController@update', 'as' => 'admin::course_type.update']);
});

// Notifications urls group
Route::group(['prefix' => 'notify'], function () {

    // Namespace url
    $shared = '\\' . $this->sharedNamespace . '\NotifyController';

    // Notifications settings
    Route::get('/', ['uses' => $shared . '@index', 'as' => 'admin::notify.index']);
    Route::post('/', ['uses' => $shared . '@update', 'as' => 'admin::notify.update']);

    // Notifications messages
    Route::get('messages', ['uses' => $shared . '@messages', 'as' => 'admin::notify.messages']);
    Route::post('messages', ['uses' => $shared . '@mark', 'as' => 'admin::notify.mark']);
});

// Statistics urls group
Route::group(['prefix' => 'statistics'], function () {
    Route::get('/', ['uses' => 'StatisticsController@index', 'as' => 'admin::statistics.index']);
    Route::get('/organization/{id}', ['uses' => 'StatisticsController@organization', 'as' => 'admin::statistics.organizations.show']);
    Route::get('/organization/{id}/login', ['uses' => 'StatisticsController@loginAsOrganization', 'as' => 'admin::statistics.organizations.login']);
    Route::get('/report', ['uses' => 'StatisticsController@exportUserList', 'as' => 'admin::statistics.report']);
    Route::get('/monthly-report', ['uses' => 'StatisticsController@exportMonthlyReport', 'as' => 'admin::statistics.monthly-report']);
});
