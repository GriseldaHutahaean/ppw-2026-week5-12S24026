<?php

use App\Http\Controllers\Api\V1\AuthTokenController;
use App\Http\Controllers\Api\V1\BukuApiController;
use App\Http\Controllers\Api\V1\PeminjamanApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::get('/bukus', [BukuApiController::class, 'index'])->name('bukus.index');
    Route::get('/bukus/{buku}/referensi-eksternal', [BukuApiController::class, 'externalReference'])
        ->whereNumber('buku')
        ->name('bukus.external-reference');
    Route::get('/bukus/{buku}', [BukuApiController::class, 'show'])
        ->whereNumber('buku')
        ->name('bukus.show');

    Route::post('/auth/login', [AuthTokenController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('auth.login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/auth/me', [AuthTokenController::class, 'me'])->name('auth.me');
        Route::post('/auth/logout', [AuthTokenController::class, 'logout'])->name('auth.logout');

        Route::post('/bukus', [BukuApiController::class, 'store'])->name('bukus.store');
        Route::put('/bukus/{buku}', [BukuApiController::class, 'replace'])
            ->whereNumber('buku')
            ->name('bukus.replace');
        Route::patch('/bukus/{buku}', [BukuApiController::class, 'update'])
            ->whereNumber('buku')
            ->name('bukus.update');
        Route::delete('/bukus/{buku}', [BukuApiController::class, 'destroy'])
            ->whereNumber('buku')
            ->name('bukus.destroy');

        Route::get('/peminjamans', [PeminjamanApiController::class, 'index'])->name('peminjamans.index');
        Route::get('/peminjamans/{peminjaman}', [PeminjamanApiController::class, 'show'])
            ->whereNumber('peminjaman')
            ->name('peminjamans.show');
        Route::post('/peminjamans', [PeminjamanApiController::class, 'store'])->name('peminjamans.store');
        Route::post('/bukus/{buku}/peminjamans', [PeminjamanApiController::class, 'store'])
            ->whereNumber('buku')
            ->name('bukus.peminjamans.store');
        Route::patch('/peminjamans/{peminjaman}/kembalikan', [PeminjamanApiController::class, 'returnBook'])
            ->whereNumber('peminjaman')
            ->name('peminjamans.return');
    });
});
