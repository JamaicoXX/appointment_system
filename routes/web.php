<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home-page')->name('home');

Route::middleware(['auth', 'isAuthorized'])->group(function () {
    Route::view('dashboard', 'admin.dashboard')->name('dashboard');
    Route::view('appointments', 'admin.appointments')->name('appointments');
    Route::view('medical-records', 'admin.medical-records')->name('medical-records');
    Route::view('patient-info', 'admin.patient-info')->name('patient-info');
    Route::view('transactions', 'admin.transactions')->name('transactions');
    Route::view('services', 'admin.services')->name('services');
    Route::view('business-day-editor', 'admin.business-days-editor')->name('business-day-editor');
});

Route::middleware(['auth'])->group(function () {
    Route::view('home', 'user.home')->name('user-home');
});

// Route::middleware(['auth', 'role:patient'])->group(function () {
//     Route::get('/patient/profile', [PatientController::class, 'profile']);
// });

require __DIR__.'/settings.php';
