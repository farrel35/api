<?php

use App\Http\Controllers\BookingServisController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SparepartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BengkelController;
use App\Http\Controllers\ProfileController;

// Universal

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/bengkel', [BengkelController::class, 'index']);
Route::get('/bengkel/{id}', [BengkelController::class, 'show']);

Route::get('/bengkel/sparepart/{id}', [SparepartController::class, 'getByBengkelId']);
Route::get('/bengkel/service/{id}', [ServiceController::class, 'getByBengkelId']);

// End Universal

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::get('/booking-servis/{id}', [BookingServisController::class, 'show']);
    Route::put('/booking-servis/{id}', [BookingServisController::class, 'update']);
});

// User

Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::post('/booking-servis', [BookingServisController::class, 'store']);
    Route::delete('/booking-servis/{id}', [BookingServisController::class, 'destroy']);
    Route::get('/user/booking-servis', [BookingServisController::class, 'getByUserId']);
});

// End User

// Owner bengkel

Route::middleware(['auth:sanctum', 'role:admin_bengkel'])->group(function () {
    Route::post('/bengkel', [BengkelController::class, 'store']);
    Route::post('/bengkel/{id}', [BengkelController::class, 'update']);
    Route::delete('/bengkel/{id}', [BengkelController::class, 'destroy']);

    Route::get('/owner/bengkel', [BengkelController::class, 'getByOwner']);
    Route::get('/owner/booking-servis', [BookingServisController::class, 'getByOwnerId']);

    Route::post('/owner/sparepart', [SparepartController::class, 'store']);
    Route::post('/owner/sparepart/{id}', [SparepartController::class, 'update']);
    Route::delete('/owner/sparepart/{id}', [SparepartController::class, 'destroy']);
    Route::get('/owner/sparepart', [SparepartController::class, 'getByOwnerId']);

    Route::post('/owner/service', [ServiceController::class, 'store']);
    Route::post('/owner/service/{id}', [ServiceController::class, 'update']);
    Route::delete('/owner/service/{id}', [ServiceController::class, 'destroy']);
    Route::get('/owner/service', [ServiceController::class, 'getByOwnerId']);
});

// End Owner bengkel

// Admin

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/booking-servis', [BookingServisController::class, 'index']);
});

// End Admin



