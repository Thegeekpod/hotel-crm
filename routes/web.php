<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('frontoffice.dashboard');
})->name('home');

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


