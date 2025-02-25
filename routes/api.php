<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UnitController;
use App\Http\Controllers\api\DivisiController;
use App\Http\Controllers\api\JabatanController;
use App\Http\Controllers\api\DepartemenController;
use App\Http\Controllers\api\LhaController;
use App\Http\Controllers\api\TemuanController;

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

    Route::prefix('unit')->group(function () {
        Route::get('/', [UnitController::class, 'index']); // Get all
        Route::get('/{id}', [UnitController::class, 'show']); // Get single
        Route::post('/', [UnitController::class, 'store']); // Create
        Route::put('/{id}', [UnitController::class, 'update']); // Update
        Route::delete('/{id}', [UnitController::class, 'destroy']); // Soft delete
        Route::put('/restore/{id}', [UnitController::class, 'restore']); // Restore deleted unit
    });

    Route::prefix('divisi')->group(function () {
        Route::get('/', [DivisiController::class, 'index']); // Get all
        Route::get('/{id}', [DivisiController::class, 'show']); // Get single
        Route::get('/find-by-unit-id/{id}', [DivisiController::class, 'findByUnitId']); // Get single
        Route::post('/', [DivisiController::class, 'store']); // Create
        Route::put('/{id}', [DivisiController::class, 'update']); // Update
        Route::delete('/{id}', [DivisiController::class, 'destroy']); // Soft delete
        Route::put('/restore/{id}', [DivisiController::class, 'restore']); // Restore deleted unit
    });
    Route::prefix('departemen')->group(function () {
        Route::get('/', [DepartemenController::class, 'index']); // Get all
        Route::get('/{id}', [DepartemenController::class, 'show']); // Get single
        Route::get('/find-by-divisi-id/{id}', [DepartemenController::class, 'findByDivisiId']); // Get single
        Route::post('/', [DepartemenController::class, 'store']); // Create
        Route::put('/{id}', [DepartemenController::class, 'update']); // Update
        Route::delete('/{id}', [DepartemenController::class, 'destroy']); // Soft delete
        Route::put('/restore/{id}', [DepartemenController::class, 'restore']); // Restore deleted unit
    });

    Route::prefix('jabatan')->group(function () {
        Route::get('/', [JabatanController::class, 'index']); // Get all
        Route::get('/{id}', [JabatanController::class, 'show']); // Get single
        Route::post('/', [JabatanController::class, 'store']); // Create
        Route::put('/{id}', [JabatanController::class, 'update']); // Update
        Route::delete('/{id}', [JabatanController::class, 'destroy']); // Soft delete
        Route::put('/restore/{id}', [JabatanController::class, 'restore']); // Restore deleted unit
    });

    Route::prefix('lha')->group(function () {
        Route::get('/', [LhaController::class, 'index']);
        Route::get('/{id}', [LhaController::class, 'show']); // Get single
        Route::post('/create', [LhaController::class, 'save']);
        Route::put('/{id}', [LhaController::class, 'update']); // Update
        Route::delete('/{id}', [LhaController::class, 'destroy']); // Soft delete
        Route::put('/restore/{id}', [LhaController::class, 'restore']); // Restore deleted unit
    });

    Route::prefix('temuan')->group(function () {
        Route::get('/', [TemuanController::class, 'index']); // Get all
        Route::get('/{id}', [TemuanController::class, 'show']); // Get single
        Route::get('/find-by-lha-id/{id}', [TemuanController::class, 'findByLhaId']); // Get single
        Route::post('/', [TemuanController::class, 'store']); // Create
        Route::put('/{id}', [TemuanController::class, 'update']); // Update
        Route::delete('/{id}', [TemuanController::class, 'destroy']); // Soft delete
        Route::put('/restore/{id}', [TemuanController::class, 'restore']); // Restore deleted unit
    });

    Route::post('logout', [AuthController::class, 'logout']);
});
