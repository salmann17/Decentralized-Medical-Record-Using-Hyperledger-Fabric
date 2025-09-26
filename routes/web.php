<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Doctor\DoctorController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsDoctor;
use App\Http\Middleware\IsPatient;

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

    Route::middleware([IsAdmin::class])
        ->prefix('admin')->name('admin.')
        ->group(function () {
            Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
            Route::get('/doctors', [AdminController::class, 'doctors'])->name('doctors');
            Route::post('/doctors/assign', [AdminController::class, 'assignDoctor'])->name('doctors.assign');
            Route::delete('/doctors/{doctorId}', [AdminController::class, 'removeDoctor'])->name('doctors.remove');
            Route::get('/patients', [AdminController::class, 'patients'])->name('patients');
            Route::get('/records', [AdminController::class, 'records'])->name('records');
            Route::get('/audit', [AdminController::class, 'audit'])->name('audit');
            Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
            Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        });

    Route::middleware([IsDoctor::class])
        ->prefix('doctor')->name('doctor.')
        ->group(function () {
            Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('dashboard');
            Route::get('/hospitals', [DoctorController::class, 'hospitals'])->name('hospitals');
        });

    Route::middleware([IsPatient::class])
        ->prefix('patient')->name('patient.')
        ->group(function () {
            Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('dashboard');
            Route::get('/records', [PatientController::class, 'records'])->name('records');
            Route::get('/records/{id}', [PatientController::class, 'recordDetail'])->name('records.detail');
            Route::get('/access-requests', [PatientController::class, 'accessRequests'])->name('access-requests');
            Route::post('/access-requests/{id}/approve', [PatientController::class, 'approveAccess'])->name('access-requests.approve');
            Route::post('/access-requests/{id}/reject', [PatientController::class, 'rejectAccess'])->name('access-requests.reject');
            Route::get('/active-doctors', [PatientController::class, 'activeDoctors'])->name('active-doctors');
            Route::delete('/active-doctors/{id}/revoke', [PatientController::class, 'revokeAccess'])->name('active-doctors.revoke');
            Route::get('/audit-trail', [PatientController::class, 'auditTrail'])->name('audit-trail');
            Route::get('/settings', [PatientController::class, 'settings'])->name('settings');
            Route::post('/settings', [PatientController::class, 'updateSettings'])->name('settings.update');
        });

});