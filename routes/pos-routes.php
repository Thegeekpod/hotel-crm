<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;

Route::prefix('pos')->name('pos.')->middleware('section:pos')->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('index');
    Route::get('/reports', [PosController::class, 'reportsIndex'])->name('reports');
    Route::get('/orders', [PosController::class, 'ordersIndex'])->name('orders.index');
    Route::get('/orders/history', [PosController::class, 'ordersIndex'])->name('orders.history');
    Route::post('/orders', [PosController::class, 'storeOrder'])->name('orders.store');
    Route::get('/orders/{order}', [PosController::class, 'getOrderDetails'])->name('orders.show');
    Route::post('/orders/{order}/status', [PosController::class, 'updateOrderStatus'])->name('orders.status');
    Route::post('/orders/{order}/items/{item}/cancel', [PosController::class, 'cancelKotItem'])->name('orders.items.cancel');
    Route::post('/orders/{order}/cancel-item', [PosController::class, 'cancelKotItem'])->name('orders.items.cancel-post');
    Route::post('/orders/{order}/update-items', [PosController::class, 'updateKotItems'])->name('orders.items.update');

    // Table management from POS
    Route::get('/tables', [PosController::class, 'getTablesList'])->name('tables.index');
    Route::get('/tables/{table}/session', [PosController::class, 'getTableSession'])->name('tables.session');
    Route::post('/tables/{table}/settle', [PosController::class, 'settleTableBill'])->name('tables.settle');
    Route::post('/tables/{table}/status', [PosController::class, 'updateTableStatus'])->name('tables.status');
    Route::post('/tables/{table}/shift', [PosController::class, 'shiftTable'])->name('tables.shift');

    // Menu catalog management
    Route::post('/items', [PosController::class, 'storeItem'])->name('items.store');
    Route::put('/items/{item}', [PosController::class, 'updateItem'])->name('items.update');
    Route::delete('/items/{item}', [PosController::class, 'deleteItem'])->name('items.destroy');
});
