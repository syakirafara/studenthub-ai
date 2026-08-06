<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OpportunityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik
|--------------------------------------------------------------------------
*/

Route::view('/', 'depan')->name('depan');

Route::get('/peluang', [OpportunityController::class, 'index'])->name('peluang.index');

/*
|--------------------------------------------------------------------------
| Hanya untuk yang BELUM masuk
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/daftar', [AuthController::class, 'formDaftar'])->name('daftar');
    Route::post('/daftar', [AuthController::class, 'daftar'])->name('daftar.simpan');

    Route::get('/masuk', [AuthController::class, 'formMasuk'])->name('masuk');
    Route::post('/masuk', [AuthController::class, 'masuk'])->name('masuk.proses');
});

/*
|--------------------------------------------------------------------------
| Hanya untuk yang SUDAH masuk
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/keluar', [AuthController::class, 'keluar'])->name('keluar');

    Route::view('/beranda', 'beranda')->name('beranda');

    /*
    |----------------------------------------------------------------------
    | Hanya untuk ADMIN
    |----------------------------------------------------------------------
    */

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::view('/', 'admin.dasbor')->name('dasbor');
    });
});
