<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Doctor\DoctorController;
use App\Http\Controllers\Patient\PatientController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// Protected routes
Route::middleware(['auth'])->group(function () {

    // Admin routes (Hospital)
    Route::middleware(['admin'])
        ->prefix('admin')->name('admin.')
        ->group(function () {
            Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
            // Add more admin routes here as needed
        });

    // Doctor routes
    Route::middleware(['doctor'])
        ->prefix('doctor')->name('doctor.')
        ->group(function () {
            Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('dashboard');
            // Add more doctor routes here as needed
        });

    // Patient routes
    Route::middleware(['patient'])
        ->prefix('patient')->name('patient.')
        ->group(function () {
            Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('dashboard');
            // Add more patient routes here as needed
        });

});
