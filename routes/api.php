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
Route::post('user-login', "ApiController@userLogin");
Route::post('user-registration', "ApiController@userReg");
Route::post('forgot-password', "ApiController@forgotPassword");


Route::group(['middleware' => 'auth:api'], function () {
    Route::post('add-product', "ApiController@addProduct");
    Route::post('edit-product', "ApiController@editProduct");
    Route::post('delete-product', "ApiController@deleteProduct");


});


