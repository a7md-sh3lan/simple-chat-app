<?php

use App\Http\Controllers\JWTAuthController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Register a new user and return a JWT token
Route::post('register', [JWTAuthController::class, 'register']);

// Authenticate a user and return a JWT token
Route::post('login', [JWTAuthController::class, 'login']);

// Logout a user and invalidate the JWT token
Route::post('logout', [JWTAuthController::class, 'logout']);

// Refresh a JWT token and return a new one
Route::post('refresh', [JWTAuthController::class, 'refresh']);

Route::middleware('auth:api')->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/log/{id}',[MessageController::class,'chatHistory']);
});
