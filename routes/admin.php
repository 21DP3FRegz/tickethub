<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('artists', \App\Http\Controllers\Admin\ArtistController::class);
    Route::resource('concerts', \App\Http\Controllers\Admin\ConcertController::class);
    Route::resource('locations', \App\Http\Controllers\Admin\LocationController::class);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
});
