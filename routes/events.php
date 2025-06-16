<?php

use Illuminate\Support\Facades\Route;

// Authenticated Event Routes
Route::middleware('auth')->group(function () {
    Route::get('/concerts', [\App\Http\Controllers\ConcertController::class, 'index'])->name('concerts.index');
    Route::get('/concerts/{concert}', [\App\Http\Controllers\ConcertController::class, 'show'])->name('concerts.show');

    Route::get('/shows/{show}/seats', [\App\Http\Controllers\SeatController::class, 'index'])->name('seats.index');

    // Reservations
    Route::post('/reservations', [\App\Http\Controllers\ReservationController::class, 'store'])->name('reservations.store');
    Route::delete('/reservations/{reservation}', [\App\Http\Controllers\ReservationController::class, 'destroy'])->name('reservations.destroy');
    Route::get('/reservations/{reservation}/booking', [\App\Http\Controllers\ReservationController::class, 'createBooking'])->name('reservations.createBooking');

    // Bookings
    Route::get('/bookings', [\App\Http\Controllers\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [\App\Http\Controllers\BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [\App\Http\Controllers\BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/print', [\App\Http\Controllers\BookingController::class, 'print'])->name('bookings.print');
    Route::delete('/bookings/{booking}', [\App\Http\Controllers\BookingController::class, 'destroy'])->name('bookings.destroy');
});
