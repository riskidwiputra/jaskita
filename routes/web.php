<?php

use App\Http\Controllers\GoogleBusinessController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPageController::class)->name('home');

Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'pages.dashboard')->name('dashboard');
    Route::view('/barang', 'pages.barang')->name('barang.index');
    Route::view('/penyewa', 'pages.penyewa')->name('penyewa.index');
    Route::view('/transaksi', 'pages.transaksi')->name('transaksi.index');
    Route::view('/denda', 'pages.denda')->name('denda.index');
    Route::view('/pengeluaran', 'pages.pengeluaran')->name('pengeluaran.index');
    Route::view('/notifikasi', 'pages.notifikasi')->name('notifikasi.index');
    Route::view('/laporan', 'pages.laporan')->name('laporan');
});

Route::view('/cetak-qr-ulasan', 'google-business.qr')->name('google-business.qr');
Route::get('/ulasan', fn () => abort(404))->name('ulasan.publik');
Route::get('/ulasan-google', fn () => abort(404))->name('google-business.reviews');

Route::middleware('auth')->group(function () {
    Route::prefix('admin/google-business')->name('google-business.')->group(function () {
        Route::get('/', [GoogleBusinessController::class, 'setup'])->name('setup');
        Route::get('/connect', [GoogleBusinessController::class, 'connect'])->name('connect');
        Route::get('/callback', [GoogleBusinessController::class, 'callback'])->name('callback');
        Route::post('/location', [GoogleBusinessController::class, 'selectLocation'])->name('location');
        Route::delete('/', [GoogleBusinessController::class, 'disconnect'])->name('disconnect');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
