<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountsController;

Route::prefix('accounts')->name('accounts.')->group(function () {
    Route::get('/', [AccountsController::class, 'index'])->name('index');
    Route::get('/export', [AccountsController::class, 'export'])->name('export');
});

