<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ProviderProfileController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ServiceDirectoryController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Public service directory
Route::get('/providers',      [ServiceDirectoryController::class, 'index']);
Route::get('/providers/{id}', [ServiceDirectoryController::class, 'show']);
Route::get('/reviews/{providerProfileId}', [ReviewController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Provider profile
    Route::get('/profile',                [ProviderProfileController::class, 'show']);
    Route::post('/profile',               [ProviderProfileController::class, 'upsert']);
    Route::post('/profile/images',        [ProviderProfileController::class, 'uploadImage']);
    Route::delete('/profile/images/{id}', [ProviderProfileController::class, 'deleteImage']);

    // Bookings
    Route::get('/bookings',                [BookingController::class, 'index']);
    Route::post('/bookings',               [BookingController::class, 'store']);
    Route::patch('/bookings/{id}/status',  [BookingController::class, 'updateStatus']);

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store']);
});
