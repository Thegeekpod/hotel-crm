<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BanquetController;

Route::prefix('banquet')->name('banquet.')->group(function () {
    Route::get('/', [BanquetController::class, 'index'])->name('index');
    
    // Room Service Orders (In-Room dining & guest amenities)
    Route::post('/room-orders', [BanquetController::class, 'storeRoomOrder'])->name('room-orders.store');
    Route::get('/room-orders/{order}', [BanquetController::class, 'getOrderDetails'])->name('room-orders.show');
    Route::post('/room-orders/{order}/status', [BanquetController::class, 'updateOrderStatus'])->name('room-orders.status');

    // Banquet Event Hall Bookings & Function Sheets
    Route::post('/bookings', [BanquetController::class, 'storeBanquetBooking'])->name('bookings.store');
    Route::get('/bookings/{booking}/function-sheet', [BanquetController::class, 'getFunctionSheet'])->name('bookings.function-sheet');
});
