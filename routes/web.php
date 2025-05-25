<?php

use App\Http\Controllers\API\PlatformController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Middleware for authentication
Route::middleware('auth:sanctum')->group(function () {
    // Dashboard
    Route::get('/dashboard', [PostController::class, 'index'])->name('dashboard');

    // Posts
    Route::prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('posts.mine'); // List posts
        Route::get('/all', [PostController::class, 'allPosts'])->name('posts.index'); // List all posts
        Route::get('/create', fn() => view('posts.create'))->name('posts.create'); // Create post form
        Route::post('/', [PostController::class, 'store'])->name('posts.store'); // Store post
        Route::get('/{id}', [PostController::class, 'show'])->name('posts.show'); // Show post details
        Route::get('/{id}/edit', [PostController::class, 'edit'])->name('posts.edit'); // Edit post form
        Route::put('/{id}', [PostController::class, 'update'])->name('posts.update'); // Update post
        Route::delete('/{id}', [PostController::class, 'destroy'])->name('posts.destroy'); // Delete post
    });

    // Platforms
    Route::prefix('platforms')->group(function () {
        Route::get('/', [PlatformController::class, 'index'])->name('platforms.index'); // List platforms
        Route::post('/toggle', [PlatformController::class, 'toggle'])->name('platforms.toggle'); // Toggle platform
        Route::get('/attached', [PlatformController::class, 'getMyAttachedPlatforms'])->name('platforms.attached'); // Attached platforms
    });
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
