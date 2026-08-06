<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SavedItemController;
use App\Http\Controllers\UnggahPosterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik
|--------------------------------------------------------------------------
*/

Route::view('/', 'depan')->name('depan');

Route::get('/peluang', [OpportunityController::class, 'index'])->name('peluang.index');
Route::get('/peluang/{peluang}', [OpportunityController::class, 'show'])->name('peluang.show');

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

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');

    Route::get('/unggah', [UnggahPosterController::class, 'create'])->name('unggah.buat');
    Route::post('/unggah', [UnggahPosterController::class, 'store'])->name('unggah.simpan');
    Route::get('/unggah/{peluang}/periksa', [UnggahPosterController::class, 'periksa'])->name('unggah.periksa');

    Route::get('/tersimpan', [SavedItemController::class, 'index'])->name('tersimpan.index');
    Route::post('/peluang/{peluang}/simpan', [SavedItemController::class, 'store'])->name('tersimpan.store');
    Route::delete('/peluang/{peluang}/simpan', [SavedItemController::class, 'destroy'])->name('tersimpan.destroy');

    /*
    |----------------------------------------------------------------------
    | Hanya untuk ADMIN
    |----------------------------------------------------------------------
    */

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::view('/', 'admin.dasbor')->name('dasbor');
    });
});
