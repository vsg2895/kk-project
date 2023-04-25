<?php

use Illuminate\Support\Facades\Route;

Route::group([], function () {
    Route::post('hook', 'WebHookController@init')->name('telegram::hook');
});
