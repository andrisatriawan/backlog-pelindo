<?php

use App\Http\Controllers\api\GneratePdfController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('cetak/temuan/{id}', [GneratePdfController::class, 'temuan']);
Route::get('cetak/monitoring/{id}', [GneratePdfController::class, 'monitoring']);
