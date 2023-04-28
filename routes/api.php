<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\EscapeRoomController;
use App\Http\Controllers\Api\User\UserController;

Route::group(['middleware' => 'api'], function () {
    Route::controller(UserController::class)->group(function () {
        Route::group(['prefix' => 'user'], function () {
            Route::delete('cancel',  'destroy');
            Route::get('info', 'profilePage');
            Route::put('update', 'update');
            Route::post('logout', 'logout');
            Route::post('login', 'login');
            Route::post('register', 'store');
            Route::get('bookings', 'bookings');
        });
    });

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::apiResource('booking', BookingController::class);
    });

    Route::apiResource('escape-rooms', EscapeRoomController::class);


    Route::get('escape-rooms/{id}/time-slots/{timeSlot}', [EscapeRoomController::class, 'checkRoomsBySlot']);
});
