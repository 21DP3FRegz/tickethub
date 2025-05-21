<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Services\ConcertService;

Route::get('/', function (ConcertService $concertService) {
    return Inertia::render('Welcome', $concertService->getHomePageData());
})->name('home');

Route::get('dashboard', function () {
    $user = auth()->user();

    // Get user's active reservations
    $reservations = $user->reservations()
        ->with(['show.concert', 'seat.row'])
        ->where('reserved_until', '>', now())
        ->get()
        ->map(function ($reservation) {
            return [
                'id' => $reservation->id,
                'concert' => $reservation->show->concert->artist,
                'show' => date('M d, Y H:i', strtotime($reservation->show->start)),
                'seat' => $reservation->seat->row->name . $reservation->seat->seat_number
            ];
        });

    // Get user's tickets
    $bookings = $user->bookings()
        ->with(['tickets.show.concert', 'tickets.seat.row'])
        ->get()
        ->flatMap(function ($booking) {
            return $booking->tickets->map(function ($ticket) use ($booking) {
                return [
                    'id' => $ticket->id,
                    'booking_id' => $booking->id,
                    'concert' => $ticket->show->concert->artist,
                    'show' => date('M d, Y H:i', strtotime($ticket->show->start)),
                    'seat' => $ticket->seat->row->name . $ticket->seat->seat_number,
                    'code' => $ticket->code
                ];
            });
        });

    // Get upcoming shows
    $upcomingShows = \App\Models\Show::with(['concert.location'])
        ->where('start', '>', now())
        ->orderBy('start')
        ->take(5)
        ->get()
        ->map(function ($show) {
            return [
                'id' => $show->concert->id,
                'artist' => $show->concert->artist,
                'date' => date('M d, Y H:i', strtotime($show->start)),
                'location' => $show->concert->location->name
            ];
        });

    return Inertia::render('Dashboard', [
        'reservations' => $reservations,
        'bookings' => $bookings,
        'upcomingShows' => $upcomingShows
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/events.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
