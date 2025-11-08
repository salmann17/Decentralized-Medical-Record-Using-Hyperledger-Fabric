<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Doctor\DoctorController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Middleware\RoleMiddleware;

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

    Route::middleware(['role:admin'])
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

    Route::middleware(['role:doctor'])
        ->prefix('doctor')->name('doctor.')
        ->group(function () {
            Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('dashboard');
            Route::get('/hospitals', [DoctorController::class, 'hospitals'])->name('hospitals');
            
            // Permintaan Akses Pasien
            Route::get('/access-requests', [DoctorController::class, 'accessRequests'])->name('access-requests');
            Route::get('/access-requests/create', [DoctorController::class, 'createAccessRequest'])->name('access-requests.create');
            Route::post('/access-requests', [DoctorController::class, 'storeAccessRequest'])->name('access-requests.store');
            Route::get('/search-patients', [DoctorController::class, 'searchPatients'])->name('search-patients');
            Route::post('/access-requests/{patientId}', [DoctorController::class, 'requestAccess'])->name('request-access');
            
            // Daftar Pasien Saya
            Route::get('/my-patients', [DoctorController::class, 'myPatients'])->name('my-patients');
            
            // Rekam Medis
            Route::get('/records', [DoctorController::class, 'records'])->name('records');
            Route::get('/patients/{patientId}/records', [DoctorController::class, 'patientRecords'])->name('patient-records');
            Route::get('/patients/{patientId}/records/create', [DoctorController::class, 'createRecord'])->name('create-record');
            Route::post('/patients/{patientId}/records', [DoctorController::class, 'storeRecord'])->name('store-record');
            Route::get('/records/{recordId}', [DoctorController::class, 'showRecord'])->name('show-record');
            Route::get('/records/{recordId}/edit', [DoctorController::class, 'showEditForm'])->name('edit-record');
            Route::get('/records/{recordId}/edit-draft', [DoctorController::class, 'editDraft'])->name('edit-draft');
            Route::put('/records/{recordId}', [DoctorController::class, 'updateRecord'])->name('update-record');
            Route::put('/records/{recordId}/draft', [DoctorController::class, 'updateDraft'])->name('update-draft');
            Route::patch('/records/{recordId}/status', [DoctorController::class, 'updateRecordStatus'])->name('update-record-status');
            Route::post('/records/{recordId}/finalize', [DoctorController::class, 'finalizeRecord'])->name('finalize-record');
            Route::post('/records/{recordId}/verify-blockchain', [DoctorController::class, 'verifyBlockchain'])->name('verify-blockchain');
            
            // Audit Trail
            Route::get('/audit-trail', [DoctorController::class, 'auditTrail'])->name('audit-trail');
            
            // Pengaturan Akun
            Route::get('/settings', [DoctorController::class, 'settings'])->name('settings');
            Route::post('/settings', [DoctorController::class, 'updateSettings'])->name('settings.update');
            Route::post('/settings/password', [DoctorController::class, 'updatePassword'])->name('settings.password.update');
        });

    Route::middleware(['role:patient'])
        ->prefix('patient')->name('patient.')
        ->group(function () {
            Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('dashboard');
            Route::get('/records', [PatientController::class, 'records'])->name('records');
            Route::get('/records/{id}', [PatientController::class, 'recordDetail'])->name('records.detail');
            Route::post('/records/{id}/verify-blockchain', [PatientController::class, 'verifyBlockchain'])->name('records.verify-blockchain');
            Route::get('/access-requests', [PatientController::class, 'accessRequests'])->name('access-requests');
            Route::post('/access-requests/{id}/approve', [PatientController::class, 'approveAccess'])->name('access-requests.approve');
            Route::post('/access-requests/{id}/reject', [PatientController::class, 'rejectAccess'])->name('access-requests.reject');
            Route::get('/active-doctors', [PatientController::class, 'activeDoctors'])->name('active-doctors');
            Route::delete('/active-doctors/{id}/revoke', [PatientController::class, 'revokeAccess'])->name('active-doctors.revoke');
            Route::get('/audit-trail', [PatientController::class, 'auditTrail'])->name('audit-trail');
            Route::get('/settings', [PatientController::class, 'settings'])->name('settings');
            Route::post('/settings', [PatientController::class, 'updateSettings'])->name('settings.update');
            Route::post('/settings/password', [PatientController::class, 'updatePassword'])->name('settings.password.update');
        });

});