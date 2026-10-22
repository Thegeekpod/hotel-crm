<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontOffice\DashboardController;
use App\Http\Controllers\FrontOffice\ArrivalController;
use App\Http\Controllers\FrontOffice\InhouseController;
use App\Http\Controllers\FrontOffice\GuestController;
use App\Http\Controllers\FrontOffice\FolioController;

// Front Office Module Routes (Arrivals, Guest CRM, In-House, Reservations)
Route::prefix('front-office')->name('frontoffice.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/fdesk', [DashboardController::class, 'index'])->name('fdesk');

    Route::get('/arrivals', [ArrivalController::class, 'index'])->name('arrivals');
    Route::post('/arrivals/{id}/status', [ArrivalController::class, 'updateStatus'])->name('arrivals.update-status');
    Route::get('/arrivals/{id}/details', [ArrivalController::class, 'getDetails'])->name('arrivals.details');
    Route::get('/guest', [GuestController::class, 'index'])->name('guest');
    Route::get('/guest-crm', [GuestController::class, 'index'])->name('guest-crm');
    Route::get('/guest/{id}/details', [GuestController::class, 'getDetails'])->name('guest.details');
    Route::post('/guest/{id}/status', [GuestController::class, 'updateStatus'])->name('guest.update-status');
    Route::post('/guest/{id}/settle', [GuestController::class, 'settle'])->name('guest.settle');
    Route::post('/guest/{id}/checkout', [GuestController::class, 'checkout'])->name('guest.checkout');

    // Folio Management (Booking table, dossier details, settlement & checkout)
    Route::get('/folio', [FolioController::class, 'index'])->name('folio');
    Route::get('/folio/{id}/details', [FolioController::class, 'getDetails'])->name('folio.details');
    Route::post('/folio/{id}/settle', [FolioController::class, 'settle'])->name('folio.settle');
    Route::post('/folio/{id}/checkout', [FolioController::class, 'checkout'])->name('folio.checkout');
    Route::get('/reserve', [DashboardController::class, 'reserve'])->name('reserve');
    
    // Dynamic Reservation, Check-in & Room Status Endpoints
    Route::post('/reserve', [DashboardController::class, 'storeReservation'])->name('reserve.store');
    Route::get('/next-reserve-id', [DashboardController::class, 'getNextReserveId'])->name('reserve.next-id');
    Route::get('/search-guest', [DashboardController::class, 'searchGuest'])->name('guest.search');
    Route::get('/check-room-availability', [DashboardController::class, 'checkRoomAvailability'])->name('room.check-availability');
    Route::post('/room/update-status', [DashboardController::class, 'updateRoomStatus'])->name('room.update-status');
});

