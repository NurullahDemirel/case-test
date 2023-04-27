<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\UserAuthController;
use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\BookingController;

Route::controller(UserAuthController::class)->group(function () {
    Route::group(['prefix' => 'user'], function () {
        Route::delete('cancel',  'destroy');
        Route::get('info', 'profilePage');
        Route::put('update', 'update');
        Route::post('logout', 'logout');
        Route::post('login', 'login');
    });
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::apiResource('booking', BookingController::class);
});
