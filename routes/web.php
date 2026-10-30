<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontOffice\GuestUploadController;

Route::get('/', function () {
    return redirect()->route('frontoffice.dashboard');
})->name('home');

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


