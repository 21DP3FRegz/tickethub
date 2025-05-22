<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConcertController extends Controller
{
    public function index(Request $request)
    {
        $query = Concert::with(['location', 'shows'])
            ->orderBy('artist');

        // Filter by artist
        if ($request->has('artist')) {
            $query->where('artist', 'like', '%' . $request->input('artist') . '%');
        }

        // Filter by location
        if ($request->has('location')) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('location') . '%');
            });
        }

        // Filter by date
        if ($request->has('date')) {
            $query->whereHas('shows', function ($q) use ($request) {
                $q->whereDate('start', $request->input('date'));
            });
        }

        $concerts = $query->get();

        return Inertia::render('concerts/Index', [
            'concerts' => $concerts,
            'filters' => $request->only(['location', 'date', 'artist']),
        ]);
    }

    public function show(Concert $concert)
    {
        // Load concert with location, shows, and the related seats and rows for each show
        $concert->load([
            'location',
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

        return Inertia::render('concerts/Show', [
            'concert' => $concert,
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
            return 'booked';
        }

        if ($seat->reservation) {
            // Check if reservation is still valid
            if ($seat->reservation->reserved_until >= now()) {
                return 'reserved';
            }
        }

        return 'available';
    }
}
