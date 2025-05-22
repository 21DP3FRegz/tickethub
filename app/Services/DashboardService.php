<?php

namespace App\Services;

use App\Models\Show;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function getDashboardData()
    {
        return [
            'reservations' => $this->getActiveReservations(),
            'bookings' => $this->getConfirmedBookings(),
            'upcomingShows' => $this->getUpcomingShows(),
        ];
    }

    private function getActiveReservations()
    {
        return Auth::user()->reservations()
            ->with(['show.concert', 'seat'])
            ->where('reserved_until', '>=', now())
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'show_id' => $reservation->show_id,
                    'concert' => $reservation->show->concert->artist,
                    'show' => (new \DateTime($reservation->show->start))->format('M d, Y h:i A'),
                    'seat' => $reservation->seat->seat_number,
                    'reserved_until' => $reservation->reserved_until,
                ];
            });
    }

    private function getConfirmedBookings()
    {
        return Auth::user()->bookings()
            ->with(['tickets.show.concert', 'tickets.seat'])
            ->orderByDesc('created_at')
            ->take(4)
            ->get()
            ->map(function ($booking) {
                return $booking->tickets->map(function ($ticket) use ($booking) {
                    return [
                        'id' => $ticket->id,
                        'booking_id' => $booking->id,
                        'concert' => $ticket->show->concert->artist,
                        'show' => (new \DateTime($ticket->show->start))->format('M d, Y h:i A'),
                        'seat' => $ticket->seat->seat_number,
                        'code' => $ticket->code,
                    ];
                });
            })
            ->flatten(1);
    }

    private function getUpcomingShows()
    {
        return Show::with(['concert.location'])
            ->where('start', '>=', now())
            ->orderBy('start')
            ->take(5)
            ->get()
            ->map(function ($show) {
                return [
                    'id' => $show->concert->id,
                    'artist' => $show->concert->artist,
                    'date' => (new \DateTime($show->start))->format('M d, Y h:i A'),
                    'location' => $show->concert->location->name,
                ];
            });
    }
}
