<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api'], function () {

    Route::post('users/login', 'AuthController@login');
    Route::post('users', 'AuthController@register');

    Route::get('user', 'UserController@index');
    Route::match(['put', 'patch'], 'user', 'UserController@update');

    Route::group(['prefix' => 'profiles'], function () {
        Route::get('{username}', 'ProfileController@show');
        Route::post('{username}/follow', 'ProfileController@follow');
        Route::delete('{username}/follow', 'ProfileController@unFollow');
    });

    Route::resource('articles', 'ArticleController', [
        'except' => [
            'create', 'edit'
        ]
    ]);

    Route::get('articles/feed', 'ArticleController@feed');
    Route::post('articles/{article}/favorite', 'ArticleController@favorite');
    Route::delete('articles/{article}/favorite', 'ArticleController@unFavorite');

    Route::resource('articles/{article}/comments', 'CommentController', [
        'only' => [
            'index', 'store', 'destroy'
        ]
    ]);

    Route::get('tags', 'TagController@index');

});