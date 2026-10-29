<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;

Route::prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('index');
    Route::post('/orders', [PosController::class, 'storeOrder'])->name('orders.store');
    Route::get('/orders/{order}', [PosController::class, 'getOrderDetails'])->name('orders.show');
    Route::post('/orders/{order}/status', [PosController::class, 'updateOrderStatus'])->name('orders.status');

    // Table management from POS
    Route::get('/tables', [PosController::class, 'getTablesList'])->name('tables.index');
    Route::post('/tables/{table}/status', [PosController::class, 'updateTableStatus'])->name('tables.status');

    // Menu catalog management
    Route::post('/items', [PosController::class, 'storeItem'])->name('items.store');
    Route::put('/items/{item}', [PosController::class, 'updateItem'])->name('items.update');
    Route::delete('/items/{item}', [PosController::class, 'deleteItem'])->name('items.destroy');
});
