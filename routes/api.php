<?php

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

Route::apiResource('/users', App\Http\Controllers\Api\UserController::class);

Route::apiResource('/profile', App\Http\Controllers\Api\ProfileController::class);

Route::apiResource('/post', App\Http\Controllers\Api\PostController::class);

Route::apiResource('/comment', App\Http\Controllers\Api\CommentController::class);

Route::apiResource('/like', App\Http\Controllers\Api\LikeController::class);

Route::apiResource('/followers', App\Http\Controllers\Api\FollowersController::class);

Route::apiResource('/following', App\Http\Controllers\Api\FollowingController::class);

