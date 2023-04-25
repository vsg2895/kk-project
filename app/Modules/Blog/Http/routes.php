<?php

/*
 * Posts Admin
 */
Route::group(['prefix' => 'admin/posts', 'middleware' => 'auth.admin'], function () {
    Route::get('/', ['uses' => 'PostController@indexAdmin', 'as' => 'blog::posts.indexAdmin']);
    Route::get('/create', ['uses' => 'PostController@create', 'as' => 'blog::posts.create']);
    Route::post('/', ['uses' => 'PostController@store', 'as' => 'blog::posts.store']);
    Route::get('/{post}/edit', ['uses' => 'PostController@edit', 'as' => 'blog::posts.edit']);
    Route::post('/{post}', ['uses' => 'PostController@update', 'as' => 'blog::posts.update']);
    Route::delete('/{post}', ['uses' => 'PostController@delete', 'as' => 'blog::posts.delete']);
});

/*
 * Posts
 */
//Route::group(['prefix' => 'posts'], function () {
    Route::get('/', ['uses' => 'PostController@index', 'as' => 'blog::index']);
    Route::get('/{post}', ['uses' => 'PostController@show', 'as' => 'blog::show']);
    Route::get('landing/{post}', ['uses' => 'PostController@landingShow', 'as' => 'blog::landing.show']);
//});

/*
 * Comments
 */
Route::group(['prefix' => 'comments', 'middleware' => 'auth'], function () {
    Route::post('/', ['uses' => 'CommentController@store', 'as' => 'blog::comments.store']);

    Route::group(['middleware' => 'auth.admin'], function () {
        Route::post('/{comment}', ['uses' => 'CommentController@update', 'as' => 'blog::comments.update']);
        Route::delete('/{comment}', ['uses' => 'CommentController@delete', 'as' => 'blog::comments.delete']);
    });
});
