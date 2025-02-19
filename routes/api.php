<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
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

Route::post('v1/login', [AuthController::class, 'login']);
Route::post('v1/refresh-token', [AuthController::class, 'refreshToken']);

Route::middleware('auth.api')->prefix('v1')->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('profile', [UserController::class, 'profile']);
    });

    Route::post('logout', [AuthController::class, 'logout']);
});
