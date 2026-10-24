<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontOffice\DashboardController;
use App\Http\Controllers\FrontOffice\ArrivalController;
use App\Http\Controllers\FrontOffice\InhouseController;
use App\Http\Controllers\FrontOffice\GuestController;
use App\Http\Controllers\FrontOffice\FolioController;
use App\Http\Controllers\FrontOffice\CheckoutController;
use App\Http\Controllers\FrontOffice\NightAuditController;
use App\Http\Controllers\FrontOffice\HousekeepingController;
use App\Http\Controllers\FrontOffice\ServiceController;

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
    Route::post('/guest/{id}/update', [GuestController::class, 'update'])->name('guest.update');
    Route::put('/guest/{id}', [GuestController::class, 'update'])->name('guest.update-put');
    Route::post('/guest/{id}/status', [GuestController::class, 'updateStatus'])->name('guest.update-status');
    Route::post('/guest/{id}/settle', [GuestController::class, 'settle'])->name('guest.settle');
    Route::post('/guest/{id}/checkout', [GuestController::class, 'checkout'])->name('guest.checkout');

    // Folio Management (Booking table, dossier details, settlement & checkout)
    Route::get('/folio', [FolioController::class, 'index'])->name('folio');
    Route::get('/folio/{id}/details', [FolioController::class, 'getDetails'])->name('folio.details');
    Route::post('/folio/{id}/settle', [FolioController::class, 'settle'])->name('folio.settle');
    Route::post('/folio/{id}/checkout', [FolioController::class, 'checkout'])->name('folio.checkout');
    Route::post('/folio/early-checkout-guest/{bookingId}', [FolioController::class, 'earlyCheckoutGuest'])->name('folio.early-checkout-guest');

    // Checkout Management
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::get('/checkout/{id}/details', [CheckoutController::class, 'getDetails'])->name('checkout.details');
    Route::post('/checkout/{id}/settle', [CheckoutController::class, 'settle'])->name('checkout.settle');
    Route::post('/checkout/{id}/checkout', [CheckoutController::class, 'checkout'])->name('checkout.checkout');
    Route::post('/checkout/early-checkout-guest/{bookingId}', [CheckoutController::class, 'earlyCheckoutGuest'])->name('checkout.early-checkout-guest');

    // Night Audit Management
    Route::get('/nightaudit', [NightAuditController::class, 'index'])->name('nightaudit');
    Route::get('/night-audit', [NightAuditController::class, 'index'])->name('night-audit');
    Route::post('/night-audit/complete', [NightAuditController::class, 'complete'])->name('night-audit.complete');
    Route::get('/night-audit/{id}/report', [NightAuditController::class, 'getReport'])->name('night-audit.report');

    // Housekeeping Management
    Route::get('/housekeeping', [HousekeepingController::class, 'index'])->name('housekeeping');
    Route::post('/housekeeping/update-status', [HousekeepingController::class, 'updateStatus'])->name('housekeeping.update-status');
    Route::post('/housekeeping/bulk-update', [HousekeepingController::class, 'bulkUpdate'])->name('housekeeping.bulk-update');
    Route::get('/housekeeping/{id}/history', [HousekeepingController::class, 'getRoomHistory'])->name('housekeeping.history');

    // Extra Services & In-Room Dining Orders
    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('index');
        Route::post('/orders', [ServiceController::class, 'storeOrder'])->name('orders.store');
        Route::get('/orders/{order}', [ServiceController::class, 'getOrderDetails'])->name('orders.show');
        Route::post('/orders/{order}/status', [ServiceController::class, 'updateOrderStatus'])->name('orders.status');
    });

    Route::get('/reserve', [DashboardController::class, 'reserve'])->name('reserve');
    
    // Dynamic Reservation, Check-in & Room Status Endpoints
    Route::post('/reserve', [DashboardController::class, 'storeReservation'])->name('reserve.store');
    Route::get('/next-reserve-id', [DashboardController::class, 'getNextReserveId'])->name('reserve.next-id');
    Route::get('/search-guest', [DashboardController::class, 'searchGuest'])->name('guest.search');
    Route::get('/check-room-availability', [DashboardController::class, 'checkRoomAvailability'])->name('room.check-availability');
    Route::post('/room/update-status', [DashboardController::class, 'updateRoomStatus'])->name('room.update-status');
});


