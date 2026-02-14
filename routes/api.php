<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AIController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Get current user
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public auth routes
Route::post('/auth/login', [AuthController::class, 'apiLogin']);
Route::post('/auth/register', [AuthController::class, 'apiRegister']);

// AI Chatbot (public - no auth required)
Route::post('/ai/chat', [AIController::class, 'chat']);

// Protected auth routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'apiLogout']);
});
