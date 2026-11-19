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
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Endpoint Admin
    Route::post('/admin/store-user', [AdminController::class, 'storeUser'])->name('admin.storeUser');
    Route::put('/admin/user/{id}', [AdminController::class, 'updateUser'])->name('admin.updateUser');
    Route::delete('/admin/user/{id}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');
    Route::post('/admin/scan-attendance', [AdminController::class, 'scanAttendance'])->name('admin.scanAttendance');
    Route::post('/admin/manual-attendance', [AdminController::class, 'manualAttendance'])->name('admin.manualAttendance');

    // Endpoint Pelatih (Data Atlet)
    Route::post('/pelatih/store-data-atlet', [DashboardController::class, 'storeDataAtlet'])->name('pelatih.storeDataAtlet');
    Route::delete('/pelatih/data-atlet/{id}', [DashboardController::class, 'deleteDataAtlet'])->name('pelatih.deleteDataAtlet');

    // Endpoint Pelatih Champions (Program Latihan, Best Time & Event)
    Route::post('/pelatih/store-program', [DashboardController::class, 'storeProgram'])->name('pelatih.storeProgram');
    Route::delete('/pelatih/program/{id}', [DashboardController::class, 'deleteProgram'])->name('pelatih.deleteProgram');

    Route::post('/pelatih/store-best-time', [DashboardController::class, 'storeBestTime'])->name('pelatih.storeBestTime');
    Route::delete('/pelatih/best-time/{id}', [DashboardController::class, 'deleteBestTime'])->name('pelatih.deleteBestTime');

    Route::post('/pelatih/store-event', [DashboardController::class, 'storeEvent'])->name('pelatih.storeEvent');
    Route::delete('/pelatih/event/{id}', [DashboardController::class, 'deleteEvent'])->name('pelatih.deleteEvent');
});