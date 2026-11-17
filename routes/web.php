<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);

    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Endpoint Admin (Kelola Akun, Scan QR, & Absensi Manual)
    Route::post('/admin/store-user', [AdminController::class, 'storeUser'])->name('admin.storeUser');
    Route::post('/admin/scan-attendance', [AdminController::class, 'scanAttendance'])->name('admin.scanAttendance');
    Route::post('/admin/manual-attendance', [AdminController::class, 'manualAttendance'])->name('admin.manualAttendance');
});