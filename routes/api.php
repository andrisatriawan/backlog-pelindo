<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UnitController;
use App\Http\Controllers\api\DivisiController;
use App\Http\Controllers\api\JabatanController;
use App\Http\Controllers\api\DepartemenController;
use App\Http\Controllers\api\FilesController;
use App\Http\Controllers\api\GneratePdfController;
use App\Http\Controllers\api\LhaController;
use App\Http\Controllers\api\RekomendasiController;
use App\Http\Controllers\api\TemuanController;
use App\Http\Controllers\api\TindakLanjutController;
use App\Http\Controllers\api\TindakLanjutHasFileController;
use App\Http\Controllers\FileController;

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
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'find']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::post('/', [UserController::class, 'store']);
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
        Route::get('/details/{id}', [LhaController::class, 'details']);
        Route::post('send-lha-to-spv', [LhaController::class, 'sendLhaToSpv']);
        Route::post('send-lha-to-pic', [LhaController::class, 'sendLhaToPic']);
        Route::post('send-lha-to-pj', [LhaController::class, 'sendLhaToPj']);
        Route::post('send-lha-to-auditor', [LhaController::class, 'sendLhaToAuditor']);
        Route::post('reject-lha', [LhaController::class, 'rejectLha']);
    });

    Route::prefix('temuan')->group(function () {
        Route::get('/', [TemuanController::class, 'index']); // Get all
        Route::get('/{id}', [TemuanController::class, 'show']); // Get single
        Route::get('/find-by-lha-id/{id}', [TemuanController::class, 'findByLhaId']); // Get single
        Route::post('/', [TemuanController::class, 'store']); // Create
        Route::put('/{id}', [TemuanController::class, 'update']); // Update
        Route::delete('/{id}', [TemuanController::class, 'destroy']); // Soft delete
        Route::put('/restore/{id}', [TemuanController::class, 'restore']); // Restore deleted unit

        Route::post('send-temuan-to-pic', [TemuanController::class, 'sendToPIC']);
        Route::post('reject-temuan', [TemuanController::class, 'rejectTemuan']);
        Route::post('submit-temuan', [TemuanController::class, 'submitTemuan']);
        Route::post('accept-temuan', [TemuanController::class, 'acceptTemuan']);

        Route::post('tolak-selesai-internal', [TemuanController::class, 'tolakSelesaiInternal']);
        Route::post('selesai-internal', [TemuanController::class, 'selesaiInternal']);

        Route::get('log-stage/{id}', [TemuanController::class, 'logStage']);
    });

    Route::prefix('rekomendasi')->group(function () {
        Route::get('/', [RekomendasiController::class, 'index']); // Get all
        Route::get('/{id}', [RekomendasiController::class, 'show']); // Get single
        Route::get('/find-by-temuan-id/{id}', [RekomendasiController::class, 'findByTemuanId']); // Get single
        Route::post('/', [RekomendasiController::class, 'store']); // Create
        Route::put('/{id}', [RekomendasiController::class, 'update']); // Update
        Route::delete('/{id}', [RekomendasiController::class, 'destroy']); // Soft delete
        Route::put('/restore/{id}', [RekomendasiController::class, 'restore']); // Restore deleted unit
    });
    Route::prefix('tindaklanjut')->group(function () {
        Route::get('/', [TindakLanjutController::class, 'index']); // Get all
        Route::get('/{id}', [TindakLanjutController::class, 'show']); // Get single
        Route::get('/find-by-rekomendasi-id/{id}', [TindakLanjutController::class, 'findByRekomendasiId']); // Get single
        Route::post('/', [TindakLanjutController::class, 'store']); // Create
        Route::put('/{id}', [TindakLanjutController::class, 'update']); // Update
        Route::delete('/{id}', [TindakLanjutController::class, 'destroy']); // Soft delete
        Route::put('/restore/{id}', [TindakLanjutController::class, 'restore']); // Restore deleted unit
    });

    Route::prefix('tindaklanjut-hasfile')->group(function () {
        Route::get('/find-by-tindaklanjut-id/{id}', [TindakLanjutHasFileController::class, 'findByTindakLanjutId']); // Get all
        Route::post('/', [TindakLanjutHasFileController::class, 'store']); // Create
        Route::put('/{id}', [TindakLanjutHasFileController::class, 'update']); // Update
        Route::delete('/{id}', [TindakLanjutHasFileController::class, 'destroy']); // Soft delete
        Route::put('/restore/{id}', [TindakLanjutHasFileController::class, 'restore']); // Restore deleted unit
    });
    Route::prefix('file')->group(function () {
        Route::get('/', [FileController::class, 'index']); // Get all
        Route::get('/{id}', [FileController::class, 'show']); // Get single
        Route::post('/', [FileController::class, 'store']); // Create
        Route::put('/{id}', [FileController::class, 'update']); // Update
        Route::delete('/{id}', [FileController::class, 'destroy']); // Soft delete
        Route::put('/restore/{id}', [FileController::class, 'restore']); // Restore deleted unit
    });


    Route::prefix('files')->group(function () {
        Route::post('upload', [FilesController::class, 'upload']);
        Route::get('find-by-lha/{id}', [FilesController::class, 'findByLha']);
        Route::get('{id}/find', [FilesController::class, 'find']);
        Route::delete('{id}/destroy', [FilesController::class, 'destroy']);
    });

    Route::prefix('cetak')->group(function () {
        Route::get('temuan/{id}', [GneratePdfController::class, 'temuan']);
    });

    Route::post('logout', [AuthController::class, 'logout']);
});
