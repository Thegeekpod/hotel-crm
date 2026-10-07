<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Utilities\ReservationModeController;
use App\Http\Controllers\Admin\Utilities\RegistrationTypeController;
use App\Http\Controllers\Admin\Utilities\TitleController;
use App\Http\Controllers\Admin\Utilities\NationalityController;
use App\Http\Controllers\Admin\Utilities\IdCardTypeController;
use App\Http\Controllers\Admin\Utilities\PaymentModeController;
use App\Http\Controllers\Admin\Utilities\RoomCategoryController;
use App\Http\Controllers\Admin\Utilities\FloorController;
use App\Http\Controllers\Admin\Utilities\BeddingConfigController;
use App\Http\Controllers\Admin\Utilities\AmenityController;
use App\Http\Controllers\Admin\Utilities\HousekeepingStateController;
use App\Http\Controllers\Admin\Utilities\OperationalStatusController;
use App\Http\Controllers\Admin\RoomManagementController;
use App\Http\Controllers\Admin\RoomMaintainController;

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
        Route::post('/bulk-delete', [RoomManagementController::class, 'bulkDestroy'])->name('bulk-destroy');
        Route::get('/{id}', [RoomManagementController::class, 'show'])->name('show');
        Route::put('/{id}', [RoomManagementController::class, 'update'])->name('update');
        Route::delete('/{id}', [RoomManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-maintenance', [RoomManagementController::class, 'toggleMaintenance'])->name('toggle-maintenance');
    });

    // Room Maintenance Module CRUD
    Route::prefix('room-maintain')->name('roommaintain.')->group(function () {
        Route::get('/', [RoomMaintainController::class, 'index'])->name('index');
        Route::post('/', [RoomMaintainController::class, 'store'])->name('store');
        Route::post('/bulk-delete', [RoomMaintainController::class, 'bulkDestroy'])->name('bulk-destroy');
        Route::get('/{id}', [RoomMaintainController::class, 'show'])->name('show');
        Route::put('/{id}', [RoomMaintainController::class, 'update'])->name('update');
        Route::delete('/{id}', [RoomMaintainController::class, 'destroy'])->name('destroy');
    });

    // Master Utilities Group
    Route::prefix('utilities')->name('utilities.')->group(function () {
        // Front Office Utilities
        Route::prefix('front-office')->name('frontoffice.')->group(function () {
            Route::post('registration-type/bulk-delete', [RegistrationTypeController::class, 'bulkDestroy'])->name('registration-type.bulk-destroy');
            Route::resource('registration-type', RegistrationTypeController::class)->except(['create', 'edit', 'show']);

            Route::post('reservation-mode/bulk-delete', [ReservationModeController::class, 'bulkDestroy'])->name('reservation-mode.bulk-destroy');
            Route::resource('reservation-mode', ReservationModeController::class)->except(['create', 'edit', 'show']);

            Route::post('title/bulk-delete', [TitleController::class, 'bulkDestroy'])->name('title.bulk-destroy');
            Route::resource('title', TitleController::class)->except(['create', 'edit', 'show']);

            Route::post('nationality/bulk-delete', [NationalityController::class, 'bulkDestroy'])->name('nationality.bulk-destroy');
            Route::resource('nationality', NationalityController::class)->except(['create', 'edit', 'show']);

            Route::post('idcard-type/bulk-delete', [IdCardTypeController::class, 'bulkDestroy'])->name('idcard-type.bulk-destroy');
            Route::resource('idcard-type', IdCardTypeController::class)->except(['create', 'edit', 'show']);

            Route::post('payment-mode/bulk-delete', [PaymentModeController::class, 'bulkDestroy'])->name('payment-mode.bulk-destroy');
            Route::resource('payment-mode', PaymentModeController::class)->except(['create', 'edit', 'show']);
        });

        // Room Management Utilities
        Route::prefix('room-management')->name('roommanage.')->group(function () {
            Route::post('category/bulk-delete', [RoomCategoryController::class, 'bulkDestroy'])->name('category.bulk-destroy');
            Route::resource('category', RoomCategoryController::class)->except(['create', 'edit', 'show']);

            Route::post('floor/bulk-delete', [FloorController::class, 'bulkDestroy'])->name('floor.bulk-destroy');
            Route::resource('floor', FloorController::class)->except(['create', 'edit', 'show']);

            Route::post('bedding-config/bulk-delete', [BeddingConfigController::class, 'bulkDestroy'])->name('bedding-config.bulk-destroy');
            Route::resource('bedding-config', BeddingConfigController::class)->except(['create', 'edit', 'show']);

            Route::post('amenity/bulk-delete', [AmenityController::class, 'bulkDestroy'])->name('amenity.bulk-destroy');
            Route::resource('amenity', AmenityController::class)->except(['create', 'edit', 'show']);
        });

        // Housekeeping Utilities
        Route::prefix('housekeeping')->name('housekeeping.')->group(function () {
            Route::post('state/bulk-delete', [HousekeepingStateController::class, 'bulkDestroy'])->name('state.bulk-destroy');
            Route::resource('state', HousekeepingStateController::class)->except(['create', 'edit', 'show']);

            Route::post('operational/bulk-delete', [OperationalStatusController::class, 'bulkDestroy'])->name('operational.bulk-destroy');
            Route::resource('operational', OperationalStatusController::class)->except(['create', 'edit', 'show']);
        });
    });
});
