<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UstadController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\PembayaranController;
use App\Models\petugaspembayaran;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 🚀 **Autentikasi**
Route::controller(AuthController::class)->group(function () {
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register');
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout')->middleware('auth');
});

// 📩 **Verifikasi Email**
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Link verifikasi telah dikirim ulang!');
    })->middleware('throttle:6,1')->name('verification.send');
});

// 🎯 **Dashboard (hanya user yang sudah login & terverifikasi)**
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// 👨‍🏫 **Manajemen Ustadz (Admin Only)**
Route::middleware(['auth', 'role:admin'])->prefix('ustadz')->name('ustadz.')->group(function () {
    Route::get('/', [UstadController::class, 'index'])->name('index');
    Route::get('/create', [UstadController::class, 'create'])->name('create');
    Route::post('/store', [UstadController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [UstadController::class, 'edit'])->name('edit');
    Route::put('/{id}', [UstadController::class, 'update'])->name('update');
    Route::delete('/{id}', [UstadController::class, 'destroy'])->name('destroy');
});

// 🏫 **Manajemen Santri**
Route::middleware(['auth'])->prefix('santri')->name('santri.')->group(function () {
    Route::get('/', [SantriController::class, 'index'])->name('index');
    Route::get('/create', [SantriController::class, 'create'])->name('create'); // ✅ Semua bisa akses
    Route::post('/store', [SantriController::class, 'store'])->name('store'); // ✅ Semua bisa tambah data

    // 🚫 **Santri tidak bisa update atau delete dirinya sendiri**
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/{id}/edit', [SantriController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SantriController::class, 'update'])->name('update');
        Route::delete('/{id}', [SantriController::class, 'destroy'])->name('destroy');
    });
});

// 🏦 **Manajemen Petugas Pembayaran (Admin Only)**
Route::middleware(['auth', 'role:admin'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/', [petugaspembayaran::class, 'index'])->name('index');
    Route::get('/create', [petugaspembayaran::class, 'create'])->name('create');
    Route::post('/store', [petugaspembayaran::class, 'store'])->name('store');
    Route::get('/{id}/edit', [petugaspembayaran::class, 'edit'])->name('edit');
    Route::put('/{id}', [petugaspembayaran::class, 'update'])->name('update');
    Route::delete('/{id}', [petugaspembayaran::class, 'destroy'])->name('destroy');
});

// 💰 **Manajemen Pembayaran (Petugas, Admin & Santri)**
Route::middleware(['auth', 'role:petugas|admin|santri'])->prefix('pembayaran')->name('pembayaran.')->group(function () {
    Route::get('/', [PembayaranController::class, 'index'])->name('index'); // 🔍 Semua bisa melihat pembayaran
    Route::get('/create', [PembayaranController::class, 'create'])->name('create'); // ✅ Santri bisa tambah pembayaran
    Route::post('/store', [PembayaranController::class, 'store'])->name('store'); // ✅ Santri bisa menyimpan pembayaran

    // 🚫 **Santri tidak bisa edit atau hapus pembayaran**
    Route::middleware(['role:petugas|admin'])->group(function () {
        Route::get('/{id}/edit', [PembayaranController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PembayaranController::class, 'update'])->name('update');
        Route::delete('/{id}', [PembayaranController::class, 'destroy'])->name('destroy');
    });
});

// 📅 **Manajemen Absensi (Admin & Ustadz)**
Route::middleware(['auth', 'role:admin|ustadz'])->prefix('absensi')->name('absensi.')->group(function () {
    Route::get('/', [AbsensiController::class, 'index'])->name('index');
    Route::get('/create', [AbsensiController::class, 'create'])->name('create');
    Route::post('/store', [AbsensiController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [AbsensiController::class, 'edit'])->name('edit');
    Route::put('/{id}', [AbsensiController::class, 'update'])->name('update');
    Route::delete('/{id}', [AbsensiController::class, 'destroy'])->name('destroy');
});
