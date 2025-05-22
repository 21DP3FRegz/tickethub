<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Seat;
use App\Models\Show;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Auth::user()->reservations()
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
                    // Format with timezone info
                    'reserved_until' => $reservation->reserved_until->toISOString(),
                ];
            });

        return redirect()->route('dashboard')->with([
            'active_reservations' => $reservations,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'show_id' => 'required|exists:shows,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id',
        ]);

        $show = Show::findOrFail($request->show_id);
        $seatIds = $request->seat_ids;
        $seats = Seat::whereIn('id', $seatIds)->get();

        // Check if user already has tickets for this show
        if (Auth::check()) {
            $existingTickets = Ticket::whereHas('booking', function ($query) {
                $query->where('user_id', Auth::id());
            })
                ->where('show_id', $show->id)
                ->count();

            if ($existingTickets > 0) {
                return back()->withErrors(['show' => 'You already have tickets for this show. You cannot purchase additional tickets for the same show.']);
            }
        }

        // Check if all seats are available
        $unavailableSeats = $seats->filter(function ($seat) {
            return $seat->reservation || $seat->ticket;
        });

        if ($unavailableSeats->count() > 0) {
            return back()->withErrors(['seat' => 'One or more selected seats are already reserved or booked.']);
        }

        // Create reservations with 15-minute duration
        $reservations = [];
        foreach ($seats as $seat) {
            $reservation = Reservation::create([
                'show_id' => $show->id,
                'seat_id' => $seat->id,
                'reservation_token' => Str::random(32),
                'reserved_until' => now()->addMinutes(15),
                'user_id' => Auth::id(),
            ]);

            \App\Jobs\ReleaseReservationJob::dispatch($reservation->id)
                ->delay($reservation->reserved_until);

            $reservations[] = $reservation;
        }

        // Redirect to booking page with the first reservation
        $firstReservation = $reservations[0];

        return redirect()->route('reservations.createBooking', $firstReservation)
            ->with('success', count($reservations) . ' seat(s) reserved for 15 minutes. Please complete your booking.')
            ->with('reservation_ids', collect($reservations)->pluck('id')->toArray());
    }

    public function destroy(Reservation $reservation)
    {
        if (! Gate::allows('delete', $reservation)) {
            abort(403, 'Unauthorized action.');
        }

        // Find all related reservations for the same show by this user
        $relatedReservations = Reservation::where('user_id', Auth::id())
            ->where('show_id', $reservation->show_id)
            ->where('reserved_until', '>=', now())
            ->get();

        // Delete all related reservations
        foreach ($relatedReservations as $relatedReservation) {
            $relatedReservation->delete();
        }

        return redirect()->route('dashboard')
            ->with('success', 'Reservation' . ($relatedReservations->count() > 1 ? 's' : '') . ' cancelled successfully.');
    }

    public function createBooking(Reservation $reservation)
    {
        Gate::authorize('view', $reservation);

        $reservation->load(['show.concert', 'seat']);

        // Get all related reservations for the same show by this user
        $relatedReservations = Reservation::where('user_id', Auth::id())
            ->where('show_id', $reservation->show_id)
            ->where('reserved_until', '>=', now())
            ->with(['seat'])
            ->get();

        return Inertia::render('bookings/Create', [
            'reservation' => [
                'id' => $reservation->id,
                'show' => [
                    'id' => $reservation->show->id,
                    'start' => $reservation->show->start,
                    'concert' => [
                        'artist' => $reservation->show->concert->artist,
                    ]
                ],
                'seat' => [
                    'seat_number' => $reservation->seat->seat_number,
                ],
                // Send timezone-aware timestamp
                'reserved_until' => $reservation->reserved_until->toISOString(),
            ],
            'relatedReservations' => $relatedReservations->map(function($r) {
                return [
                    'id' => $r->id,
                    'seat' => [
                        'seat_number' => $r->seat->seat_number,
                    ]
                ];
            }),
            'userEmail' => Auth::user()->email,
        ]);
    }
}
