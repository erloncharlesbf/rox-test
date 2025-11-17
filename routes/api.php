<?php

use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\MovieController;
use Illuminate\Support\Facades\Route;

Route::name('auth.')->controller(AuthController::class)->group(function (): void {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
    Route::post('forgot-password', 'forgotPassword')->name('forgot-password');
});

Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::get('attachments/{attachment}', [AttachmentController::class, 'show'])->name('attachments.show');
Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::apiResource('books', BookController::class);
    Route::apiResource('movies', MovieController::class);
});
