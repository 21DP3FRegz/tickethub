<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Services\ConcertService;
use App\Services\DashboardService;

Route::get('/', function (ConcertService $concertService) {
    return Inertia::render('Welcome', $concertService->getHomePageData());
})->name('home');

Route::get('/dashboard', function (DashboardService $dashboardService) {
    return Inertia::render('Dashboard', $dashboardService->getDashboardData());
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/events.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
