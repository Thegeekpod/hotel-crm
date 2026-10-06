<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoomManagementController;

// Room Management Module Routes (Room Grid, Housekeeping Status, Maintenance Logs)
Route::prefix('room-management')->name('roommanage.')->group(function () {
    Route::get('/', [RoomManagementController::class, 'index'])->name('index');
    Route::post('/', [RoomManagementController::class, 'store'])->name('store');
    Route::get('/{id}', [RoomManagementController::class, 'show'])->name('show');
    Route::put('/{id}', [RoomManagementController::class, 'update'])->name('update');
    Route::delete('/{id}', [RoomManagementController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/toggle-maintenance', [RoomManagementController::class, 'toggleMaintenance'])->name('toggle-maintenance');
});
