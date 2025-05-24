<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Booking;
use App\Models\Concert;
use App\Models\Location;
use App\Models\Reservation;
use App\Models\Show;
use App\Models\User;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_artists' => Artist::count(),
            'total_concerts' => Concert::count(),
            'total_locations' => Location::count(),
            'total_shows' => Show::count(),
            'total_bookings' => Booking::count(),
            'active_reservations' => Reservation::where('reserved_until', '>=', now())->count(),
            'upcoming_shows' => Show::where('start', '>=', now())->count(),
        ];

        $recentBookings = Booking::with(['user', 'tickets.show.concert.artist'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($booking) {
                $firstTicket = $booking->tickets->first();
                $concertName = 'N/A';

                if ($firstTicket && $firstTicket->show && $firstTicket->show->concert) {
                    if ($firstTicket->show->concert->artist) {
                        $concertName = is_object($firstTicket->show->concert->artist)
                            ? $firstTicket->show->concert->artist->name
                            : $firstTicket->show->concert->artist;
                    }
                }

                return [
                    'id' => $booking->id,
                    'user_name' => $booking->user->name,
                    'user_email' => $booking->user->email,
                    'tickets_count' => $booking->tickets->count(),
                    'concert_name' => $concertName,
                    'created_at' => $booking->created_at->format('M d, Y H:i'),
                ];
            });

        $upcomingShows = Show::with(['concert.artist', 'concert.location'])
            ->where('start', '>=', now())
            ->orderBy('start')
            ->take(5)
            ->get()
            ->map(function ($show) {
                $artistName = 'Unknown Artist';

                if ($show->concert && $show->concert->artist) {
                    $artistName = is_object($show->concert->artist)
                        ? $show->concert->artist->name
                        : $show->concert->artist;
                }

                // Handle both string and Carbon date formats
                $startDate = 'N/A';
                if ($show->start) {
                    try {
                        // If it's already a Carbon instance, use it directly
                        if ($show->start instanceof \Carbon\Carbon) {
                            $startDate = $show->start->format('M d, Y H:i');
                        } else {
                            // If it's a string, parse it first
                            $startDate = Carbon::parse($show->start)->format('M d, Y H:i');
                        }
                    } catch (\Exception $e) {
                        // If parsing fails, keep 'N/A'
                        $startDate = 'N/A';
                    }
                }

                return [
                    'id' => $show->id,
                    'artist_name' => $artistName,
                    'location_name' => $show->concert->location->name ?? 'Unknown Location',
                    'start_date' => $startDate,
                    'tickets_sold' => $show->tickets()->count(),
                    'total_seats' => $show->seats()->count(),
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentBookings' => $recentBookings,
            'upcomingShows' => $upcomingShows,
        ]);
    }
}
