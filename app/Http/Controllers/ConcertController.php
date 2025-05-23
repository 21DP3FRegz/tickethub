<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use App\Models\Artist;
use App\Models\Location;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ConcertController extends Controller
{
    public function index(Request $request)
    {
        $query = Concert::with(['location', 'shows', 'artist'])
            ->join('artists', 'concerts.artist_id', '=', 'artists.id')
            ->select('concerts.*');

        // Search functionality
        if ($request->has('search') && !empty($request->input('search'))) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('artist', function($artistQuery) use ($searchTerm) {
                    $artistQuery->where('name', 'like', $searchTerm);
                })
                    ->orWhereHas('location', function($locationQuery) use ($searchTerm) {
                        $locationQuery->where('name', 'like', $searchTerm);
                    });
            });
        }

        // Filter by artist
        if ($request->has('artist') && !empty($request->input('artist'))) {
            $query->whereHas('artist', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('artist') . '%');
            });
        }

        // Filter by artist_id
        if ($request->has('artist_id') && !empty($request->input('artist_id'))) {
            $query->where('artist_id', $request->input('artist_id'));
        }

        // Filter by location
        if ($request->has('location') && !empty($request->input('location'))) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('location') . '%');
            });
        }

        // Filter by location_id
        if ($request->has('location_id') && !empty($request->input('location_id'))) {
            $query->where('location_id', $request->input('location_id'));
        }

        // Filter by date
        if ($request->has('date') && !empty($request->input('date'))) {
            $query->whereHas('shows', function ($q) use ($request) {
                $q->whereDate('start', $request->input('date'));
            });
        }

        // Get concerts first, then sort by date
        $concerts = $query->get();

        // Handle sorting by date (default behavior)
        $sortField = $request->input('sort', 'date');
        $sortDirection = $request->input('sort_direction', 'asc');

        if ($sortField === 'date') {
            // Sort concerts by their next show date
            $concerts = $concerts->sort(function ($a, $b) use ($sortDirection) {
                $dateA = $this->getNextShowDate($a);
                $dateB = $this->getNextShowDate($b);

                // Handle cases where concerts might not have shows
                if (!$dateA && !$dateB) return 0;
                if (!$dateA) return 1;
                if (!$dateB) return -1;

                $comparison = strtotime($dateA) <=> strtotime($dateB);

                return $sortDirection === 'desc' ? -$comparison : $comparison;
            });

            // Reset array keys after sorting
            $concerts = $concerts->values();
        } else {
            // Default sorting by artist name
            $concerts = $concerts->sortBy('artist.name')->values();
        }

        // Get user's bookings for these concerts if user is logged in
        $userBookings = [];
        if (Auth::check()) {
            // Get all shows for these concerts
            $showIds = $concerts->flatMap(function ($concert) {
                return $concert->shows->pluck('id');
            })->toArray();

            // Get user's tickets for these shows
            $userTickets = Ticket::whereHas('booking', function ($query) {
                $query->where('user_id', Auth::id());
            })
                ->whereIn('show_id', $showIds)
                ->with(['show.concert', 'seat.row'])
                ->get();

            // Group tickets by concert
            foreach ($userTickets as $ticket) {
                $concertId = $ticket->show->concert->id;
                if (!isset($userBookings[$concertId])) {
                    $userBookings[$concertId] = [
                        'concert_id' => $concertId,
                        'concert_name' => $ticket->show->concert->artist->name,
                        'shows' => []
                    ];
                }

                $showId = $ticket->show->id;
                if (!isset($userBookings[$concertId]['shows'][$showId])) {
                    $userBookings[$concertId]['shows'][$showId] = [
                        'show_id' => $showId,
                        'show_date' => (new \DateTime($ticket->show->start))->format('M d, Y h:i A'),
                        'seats' => []
                    ];
                }

                $userBookings[$concertId]['shows'][$showId]['seats'][] = [
                    'id' => $ticket->seat->id,
                    'seat_number' => $ticket->seat->seat_number,
                    'row_name' => $ticket->seat->row->name,
                    'ticket_id' => $ticket->id,
                    'ticket_code' => $ticket->code
                ];
            }

            // Convert shows from associative to indexed arrays
            foreach ($userBookings as &$booking) {
                $booking['shows'] = array_values($booking['shows']);
            }
        }

        // Get all locations and artists for filter dropdowns
        $locations = Location::orderBy('name')->get();
        $artists = Artist::orderBy('name')->get();

        return Inertia::render('concerts/Index', [
            'concerts' => $concerts,
            'filters' => $request->only(['location', 'date', 'artist', 'search', 'location_id', 'artist_id', 'sort', 'sort_direction']),
            'userBookings' => $userBookings,
            'locations' => $locations,
            'artists' => $artists
        ]);
    }

    /**
     * Get the next show date for a concert
     *
     * @param \App\Models\Concert $concert
     * @return string|null
     */
    private function getNextShowDate($concert)
    {
        if (!$concert->shows || $concert->shows->count() === 0) {
            return null;
        }

        // Sort shows by date
        $sortedShows = $concert->shows->sortBy('start');

        // Find the first show that's in the future
        $now = now();
        $futureShows = $sortedShows->filter(function ($show) use ($now) {
            return strtotime($show->start) > $now->timestamp;
        });

        return $futureShows->count() > 0
            ? $futureShows->first()->start
            : $sortedShows->first()->start;
    }

    public function show(Concert $concert)
    {
        // Load concert with location, shows, and the related seats and rows for each show
        $concert->load([
            'location',
            'artist',
            'shows' => function ($query) {
                $query->where('start', '>=', now())->orderBy('start');
            },
            'shows.seats.reservation', // Load reservations for each seat
            'shows.seats.ticket',      // Load tickets for each seat
            'shows.rows'               // Load rows for each show
        ]);

        // Add seat status information for better frontend handling
        $concert->shows->each(function ($show) {
            $show->seats->each(function ($seat) {
                // Add explicit status property
                $seat->status = $this->determineSeatStatus($seat);
            });
        });

        // Get user's bookings for this concert if user is logged in
        $userBookings = null;
        if (Auth::check()) {
            $showIds = $concert->shows->pluck('id')->toArray();

            // Get user's tickets for these shows
            $userTickets = Ticket::whereHas('booking', function ($query) {
                $query->where('user_id', Auth::id());
            })
                ->whereIn('show_id', $showIds)
                ->with(['show', 'seat.row', 'booking'])
                ->get();

            if ($userTickets->count() > 0) {
                $userBookings = [
                    'concert_id' => $concert->id,
                    'concert_name' => $concert->artist->name,
                    'shows' => []
                ];

                // Group tickets by show
                foreach ($userTickets as $ticket) {
                    $showId = $ticket->show->id;
                    if (!isset($userBookings['shows'][$showId])) {
                        $userBookings['shows'][$showId] = [
                            'show_id' => $showId,
                            'show_date' => (new \DateTime($ticket->show->start))->format('M d, Y h:i A'),
                            'booking_id' => $ticket->booking->id,
                            'booking_date' => (new \DateTime($ticket->booking->created_at))->format('M d, Y'),
                            'seats' => []
                        ];
                    }

                    $userBookings['shows'][$showId]['seats'][] = [
                        'id' => $ticket->seat->id,
                        'seat_number' => $ticket->seat->seat_number,
                        'row_name' => $ticket->seat->row->name,
                        'ticket_id' => $ticket->id,
                        'ticket_code' => $ticket->code
                    ];
                }

                // Convert shows from associative to indexed arrays
                $userBookings['shows'] = array_values($userBookings['shows']);
            }
        }

        return Inertia::render('concerts/Show', [
            'concert' => $concert,
            'userBookings' => $userBookings
        ]);
    }

    /**
     * Determine the status of a seat
     *
     * @param \App\Models\Seat $seat
     * @return string
     */
    private function determineSeatStatus($seat)
    {
        if ($seat->ticket) {
            // Check if the ticket belongs to the current user
            if (Auth::check() && $seat->ticket->booking && $seat->ticket->booking->user_id === Auth::id()) {
                return 'your-booking';
            }
            return 'booked';
        }

        if ($seat->reservation) {
            // Check if reservation is still valid
            if ($seat->reservation->reserved_until >= now()) {
                // Check if the reservation belongs to the current user
                if (Auth::check() && $seat->reservation->user_id === Auth::id()) {
                    return 'your-reservation';
                }
                return 'reserved';
            }
        }

        return 'available';
    }
}
