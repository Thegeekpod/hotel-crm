<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;

Route::prefix('stores')->name('stores.')->group(function () {
    Route::get('/', [StoreController::class, 'index'])->name('index');
    
    // Menu Category Management (Store creates Menus)
    Route::post('/categories', [StoreController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [StoreController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [StoreController::class, 'deleteCategory'])->name('categories.destroy');

    // Menu Food Items & Ingredient Yield Management
    Route::post('/items', [StoreController::class, 'storeItem'])->name('items.store');
    Route::get('/items/{item}', [StoreController::class, 'getItemDetails'])->name('items.show');
    Route::put('/items/{item}', [StoreController::class, 'updateItem'])->name('items.update');
    Route::delete('/items/{item}', [StoreController::class, 'deleteItem'])->name('items.destroy');

    // Inward Receiving ("Koto ki item duklo & kota plate hobe") & Kitchen Consumption
    Route::post('/inward', [StoreController::class, 'inwardStock'])->name('inward');
    Route::post('/issue', [StoreController::class, 'issueStock'])->name('issue');
});
