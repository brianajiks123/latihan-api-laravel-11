<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Register User
Route::post('register', [AuthController::class, 'register']);

// Login
Route::post('login', [AuthController::class, 'login']);
