<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\AdminAuthController;

Route::controller(AdminAuthController::class)->group(function () {
    Route::post('login', 'loginAsAdmin');
    Route::post('login', 'loginAsAdmin');

    Route::group(['middleware' => ['role:Admin']], function () {
        // This action will be enter is user with admin role
    });
});
