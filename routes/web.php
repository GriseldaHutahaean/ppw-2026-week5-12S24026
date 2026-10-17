<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PeminjamanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Rute mutasi: wajib login
Route::middleware('auth')->group(function () {
    Route::resource('bukus', BukuController::class)->except(['index', 'show']);
    Route::resource('kategoris', KategoriController::class)->except(['index', 'show']);

    Route::get('/peminjamans', [PeminjamanController::class, 'index'])->name('peminjamans.index');
    Route::post('/bukus/{id}/pinjam', [PeminjamanController::class, 'pinjam'])->name('peminjamans.pinjam');
    Route::post('/peminjamans/{id}/kembali', [PeminjamanController::class, 'kembali'])->name('peminjamans.kembali');
});

// Rute baca: publik
Route::resource('bukus', BukuController::class)->only(['index', 'show']);
Route::resource('kategoris', KategoriController::class)->only(['index', 'show']);
