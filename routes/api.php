<?php

use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([
    "middleware" => "auth:sanctum"
], function () {
    Route::get('/userprofile', [AuthController::class, 'userProfile']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/userresource', [AuthController::class, 'userResource']);
    Route::get('/userresourcecollection', [AuthController::class, 'userResourceCollection']);
});

// Posts
Route::apiResource('/posts', PostController::class);
