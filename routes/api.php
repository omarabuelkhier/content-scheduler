<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\PlatformController;


// Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group(function () {
    // Profile Management Routes
    Route::prefix('profile')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
    });

    // Post Management Routes
    Route::prefix('posts')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [PostController::class, 'index']);
        Route::get('/all', [PostController::class, 'allPosts']);
        Route::post('/', [PostController::class, 'store']);
        Route::get('/{id}', [PostController::class, 'show']);
        Route::put('/{id}', [PostController::class, 'update']);
        Route::delete('/{id}', [PostController::class, 'destroy']);
    });

    // Platform Management Routes
    Route::prefix('platforms')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [PlatformController::class, 'index']);
        Route::post('/toggle', [PlatformController::class, 'toggle']);
        Route::get('/attached', [PlatformController::class, 'getMyAttachedPlatforms']);
    });
});
