<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

if (env("APP_DEBUG")) {
        Route::get('/demo', function () {
        return view('demo');
    });
}

Auth::routes(['register' => false]);

if (env("USE_2FA")) {
    Route::get('auth/token', 'Auth\TwoFactorController@showTokenForm');
    Route::post('auth/token', 'Auth\TwoFactorController@validateTokenForm');
    Route::post('auth/two-factor', 'Auth\TwoFactorController@setupTwoFactorAuth');
}

Route::group(['middleware' => 'auth'], function () {
    Route::get('admin', 'AdminController@handle');
    Route::get('admin/webchat-setting', 'AdminController@handle');
    Route::get('admin/webchat-setting/{id}', 'AdminController@handle');
    Route::get('admin/chatbot-users', 'AdminController@handle');
    Route::get('admin/chatbot-users/{id}', 'AdminController@handle');
    Route::get('admin/chatbot-users/{id}/conversation-log', 'AdminController@handle');
});
