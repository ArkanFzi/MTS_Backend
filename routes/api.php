<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\F1_Register\Controllers\RegisterController;
use Modules\Auth\F2_Login\Controllers\LoginController;
use Modules\Auth\F3_Logout\Controllers\LogoutController; // <-- Import controller baru

// Route yang bisa diakses publik (tanpa login)
Route::post('/auth/register', [RegisterController::class, 'register']);
Route::post('/auth/login', [LoginController::class, 'login']);

// Route yang WAJIB login dulu (harus bawa Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [LogoutController::class, 'logout']);
});