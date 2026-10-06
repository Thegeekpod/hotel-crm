<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Utilities\ReservationModeController;
use App\Http\Controllers\Admin\Utilities\IdCardTypeController;
use App\Http\Controllers\Admin\Utilities\PaymentModeController;
use App\Http\Controllers\Admin\Utilities\RoomCategoryController;
use App\Http\Controllers\Admin\Utilities\FloorController;
use App\Http\Controllers\Admin\Utilities\BeddingConfigController;
use App\Http\Controllers\Admin\Utilities\PaxCapacityController;
use App\Http\Controllers\Admin\Utilities\AmenityController;
use App\Http\Controllers\Admin\Utilities\MaintenanceReasonController;
use App\Http\Controllers\Admin\Utilities\MaintenanceEngineerController;
use App\Http\Controllers\Admin\Utilities\HousekeepingStateController;
use App\Http\Controllers\Admin\Utilities\OperationalStatusController;
use App\Http\Controllers\Admin\RoomManagementController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    })->name('index');

    Route::get('/dashboard', function () {
        return redirect()->route('admin.utilities.roommanage.category.index');
    })->name('dashboard');

    Route::get('/login', function () {
        return view('admin.auth.login');
    })->name('login');

    // Room Management Core CRUD & Operations
    Route::prefix('room-management')->name('roommanagement.')->group(function () {
        Route::get('/', [RoomManagementController::class, 'index'])->name('index');
        Route::post('/', [RoomManagementController::class, 'store'])->name('store');
        Route::get('/{id}', [RoomManagementController::class, 'show'])->name('show');
        Route::put('/{id}', [RoomManagementController::class, 'update'])->name('update');
        Route::delete('/{id}', [RoomManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-maintenance', [RoomManagementController::class, 'toggleMaintenance'])->name('toggle-maintenance');
    });

    // Master Utilities Group
    Route::prefix('utilities')->name('utilities.')->group(function () {
        // Front Office Utilities
        Route::prefix('front-office')->name('frontoffice.')->group(function () {
            Route::resource('reservation-mode', ReservationModeController::class)->except(['create', 'edit', 'show']);
            Route::resource('idcard-type', IdCardTypeController::class)->except(['create', 'edit', 'show']);
            Route::resource('payment-mode', PaymentModeController::class)->except(['create', 'edit', 'show']);
        });

        // Room Management Utilities
        Route::prefix('room-management')->name('roommanage.')->group(function () {
            Route::resource('category', RoomCategoryController::class)->except(['create', 'edit', 'show']);
            Route::resource('floor', FloorController::class)->except(['create', 'edit', 'show']);
            Route::resource('bedding-config', BeddingConfigController::class)->except(['create', 'edit', 'show']);
            Route::resource('pax-capacity', PaxCapacityController::class)->except(['create', 'edit', 'show']);
            Route::resource('amenity', AmenityController::class)->except(['create', 'edit', 'show']);
            Route::resource('maintenance-reason', MaintenanceReasonController::class)->except(['create', 'edit', 'show']);
            Route::resource('engineer', MaintenanceEngineerController::class)->except(['create', 'edit', 'show']);
        });

        // Housekeeping Utilities
        Route::prefix('housekeeping')->name('housekeeping.')->group(function () {
            Route::resource('state', HousekeepingStateController::class)->except(['create', 'edit', 'show']);
            Route::resource('operational', OperationalStatusController::class)->except(['create', 'edit', 'show']);
        });
    });
});
