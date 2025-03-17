<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', fn() => response()->json(['message' => 'Welcome Admin']));
});

Route::middleware(['auth:sanctum', 'role:owner_bengkel'])->group(function () {
    Route::get('/owner/dashboard', fn() => response()->json(['message' => 'Welcome Owner']));
});

Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::get('/user/dashboard', fn() => response()->json(['message' => 'Welcome User']));
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

