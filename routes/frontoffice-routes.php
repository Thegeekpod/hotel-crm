<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontOffice\DashboardController;

// Front Office Module Routes (Arrivals, Guest CRM, In-House, Reservations)
Route::prefix('front-office')->name('frontoffice.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/fdesk', [DashboardController::class, 'index'])->name('fdesk');

    // Placeholders for secondary tabs
    Route::get('/arrivals', [DashboardController::class, 'index'])->name('arrivals');
    Route::get('/inhouse', [DashboardController::class, 'index'])->name('inhouse');
    Route::get('/guest-crm', [DashboardController::class, 'index'])->name('guest-crm');
    Route::get('/reserve', [DashboardController::class, 'index'])->name('reserve');
});
