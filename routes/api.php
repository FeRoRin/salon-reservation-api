<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Client\ReservationController as ClientReservationController;
use App\Http\Controllers\Client\ServiceController;
use App\Http\Controllers\Client\GuestBookingController;
use App\Http\Controllers\SuperAdmin\AdminController;
use App\Http\Controllers\SuperAdmin\BusinessSettingsController;
use App\Http\Controllers\SuperAdmin\ServiceController as SuperAdminServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:10,1');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');

Route::get('/services', [ServiceController::class, 'index']);

Route::get('/business-settings', [BusinessSettingsController::class, 'show']);

// booking by guest
Route::post('/guest-booking', [GuestBookingController::class, 'store']);
//Route::post('/guest-booking', [App\Http\Controllers\Client\GuestBookingController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    /*
    |--------------------------------------------------------------------------
    | Client Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:client')->group(function () {
        Route::post('/reservations', [ClientReservationController::class, 'store']);
        Route::get('/my-reservations', [ClientReservationController::class, 'myReservations']);
        Route::delete('/reservation/{id}', [ClientReservationController::class, 'cancel']);
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (Admin + Super Admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,super_admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/reservations', [AdminReservationController::class, 'index']);
        Route::post('/reservations', [AdminReservationController::class, 'store']);
        Route::put('/reservations/{id}', [AdminReservationController::class, 'update']);
    });

    /*
    |--------------------------------------------------------------------------
    | Super Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:super_admin')->group(function () {
        Route::post('/services', [SuperAdminServiceController::class, 'store']);
        Route::put('/services/{id}', [SuperAdminServiceController::class, 'update']);
        Route::delete('/services/{id}', [SuperAdminServiceController::class, 'destroy']);

        Route::put('/business-settings', [BusinessSettingsController::class, 'update']);

        Route::post('/create-admin', [AdminController::class, 'createAdmin']);
    });
});
