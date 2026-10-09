<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontOffice\DashboardController;
use App\Http\Controllers\FrontOffice\ArrivalController;
use App\Http\Controllers\FrontOffice\InhouseController;
use App\Http\Controllers\FrontOffice\GuestController;

// Front Office Module Routes (Arrivals, Guest CRM, In-House, Reservations)
Route::prefix('front-office')->name('frontoffice.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/fdesk', [DashboardController::class, 'index'])->name('fdesk');

    Route::get('/arrivals', [ArrivalController::class, 'index'])->name('arrivals');
    Route::get('/inhouse', [InhouseController::class, 'index'])->name('inhouse');
    Route::get('/guest', [GuestController::class, 'index'])->name('guest');
    Route::get('/guest-crm', [GuestController::class, 'index'])->name('guest-crm');
    Route::get('/reserve', [DashboardController::class, 'index'])->name('reserve');
    
    // Dynamic Reservation & Check-in Endpoints
    Route::post('/reserve', [DashboardController::class, 'storeReservation'])->name('reserve.store');
    Route::get('/next-reserve-id', [DashboardController::class, 'getNextReserveId'])->name('reserve.next-id');
    Route::get('/search-guest', [DashboardController::class, 'searchGuest'])->name('guest.search');
});

