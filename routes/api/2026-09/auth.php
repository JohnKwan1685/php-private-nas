<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])
  ->name('auth.login');

Route::post('/register', [AuthController::class, 'register'])
  ->name('auth.register');

Route::post('/refresh', [AuthController::class, 'refresh'])
  ->name('auth.refresh');
