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
    Route::get('categories/{slug}/products', 'CategoryController@showProducts');
    Route::post('categories', 'CategoryController@store')->middleware('auth:api');
    Route::patch('categories/{category}', 'CategoryController@update')->middleware('auth:api');
    Route::delete('categories/{category}', 'CategoryController@destroy')->middleware('auth:api');
    Route::patch('categories/{id}/restore', 'CategoryController@restore')->middleware('auth:api');
    Route::delete('categories/{id}/delete', 'CategoryController@forceDestroy')->middleware('auth:api');

    Route::get('products', 'ProductController@index');
    Route::get('products/{product}', 'ProductController@show');
    Route::post('products', 'ProductController@store')->middleware('auth:api');
    Route::patch('products/{product}', 'ProductController@update')->middleware('auth:api');
    Route::delete('products/{product}', 'ProductController@destroy')->middleware('auth:api');
    Route::patch('products/{id}/restore', 'ProductController@restore')->middleware('auth:api');
    Route::delete('products/{id}/delete', 'ProductController@forceDestroy')->middleware('auth:api');
    Route::get('wishlist', 'ProductController@wishlist')->middleware('auth:api');
    Route::post('products/wishlist', 'ProductController@addToWishlist')->middleware('auth:api');
    Route::delete('products/{id}/wishlist', 'ProductController@removeFromWishlist')->middleware('auth:api');

    Route::get('cart', 'OrderController@cart')->middleware('auth:api');
    Route::post('orders/cart', 'OrderController@addToCart')->middleware('auth:api');
    Route::delete('orders/{id}/cart', 'OrderController@removeFromCart')->middleware('auth:api');
    Route::post('checkout', 'OrderController@store')->middleware('auth:api');
});
