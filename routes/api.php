<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::prefix('v1')->group(function() {
    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('auth')->group(function() {
        Route::post('login', 'AuthController@login');
        Route::post('register', 'AuthController@register');
    });

    Route::get('categories', 'CategoryController@index');
    Route::get('categories/{slug}', 'CategoryController@show');
    Route::post('categories', 'CategoryController@store')->middleware('auth:api');
    Route::patch('categories/{category}', 'CategoryController@update')->middleware('auth:api');
    Route::delete('categories/{category}', 'CategoryController@destroy')->middleware('auth:api');
});
