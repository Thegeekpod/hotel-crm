<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FrontOffice\GuestUploadController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route(Auth::user()->getDefaultRoute());
    }
    return redirect()->route('admin.login');
})->name('home');

// Unified Authentication Routes for both Admin & Staff/Employees
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.perform');

// Public Mobile Camera Scan & Upload Routes (5-minute security validity)
Route::get('/guest-scan-upload/{token}', [GuestUploadController::class, 'showMobileUpload'])->name('guest.mobile-upload');
Route::post('/guest-scan-upload/{token}', [GuestUploadController::class, 'submitMobileUpload'])->name('guest.mobile-upload.submit');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin-routes.php';
require __DIR__.'/frontoffice-routes.php';
require __DIR__.'/roommanage-routes.php';
require __DIR__.'/banquet-routes.php';
require __DIR__.'/pos-routes.php';
require __DIR__.'/stores-routes.php';
require __DIR__.'/accounts-routes.php';


