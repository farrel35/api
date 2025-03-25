<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BengkelController;
use App\Http\Controllers\ProfileController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);

    Route::get('/bengkel', [BengkelController::class, 'index']);
    Route::get('/bengkel/{id}', [BengkelController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', fn() => response()->json(['message' => 'Welcome Admin']));
});

Route::middleware(['auth:sanctum', 'role:owner_bengkel'])->group(function () {
    Route::post('/bengkel', [BengkelController::class, 'store']);
    Route::put('/bengkel/{id}', [BengkelController::class, 'update']);
    Route::delete('/bengkel/{id}', [BengkelController::class, 'destroy']);

    Route::get('/owner/bengkel', [BengkelController::class, 'getByOwner']);
});

Route::middleware(['auth:sanctum', 'role:user'])->group(function () {

});

