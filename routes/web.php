<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForeignerController;

// Route Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Route Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard & Map
    Route::get('/', [ForeignerController::class, 'index'])->name('map.index');
    Route::get('/api/foreigners', [ForeignerController::class, 'getLocations'])->name('foreigners.api');

    // Halaman Data WNA & CRUD Operations
    Route::get('/wna', [ForeignerController::class, 'listData'])->name('wna.index');
    Route::post('/foreigners', [ForeignerController::class, 'store'])->name('foreigners.store');
    Route::post('/foreigners/import', [ForeignerController::class, 'import'])->name('foreigners.import');
    Route::put('/foreigners/{id}', [ForeignerController::class, 'update'])->name('foreigners.update');
    Route::delete('/foreigners/{id}', [ForeignerController::class, 'destroy'])->name('foreigners.destroy');
});