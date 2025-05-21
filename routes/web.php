<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Services\ConcertService;

Route::get('/', function (ConcertService $concertService) {
    return Inertia::render('Welcome', $concertService->getHomePageData());
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/events.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
