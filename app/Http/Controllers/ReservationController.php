<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Seat;
use App\Models\Show;
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
            ->get();

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
        $seats = Seat::whereIn('id', $seatIds)
            ->with(['reservation', 'ticket'])
            ->get();

        // Check if all seats are available
        $unavailableSeats = $seats->filter(function ($seat) {
            // Check for ticket
            if ($seat->ticket) {
                return true;
            }

            // Check for valid reservation
            if ($seat->reservation && $seat->reservation->reserved_until >= now()) {
                return true;
            }

            return false;
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
        // The booking process will need to be updated to handle multiple reservations
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

        $reservation->delete();
        return redirect()->route('dashboard')->with('success', 'Reservation cancelled.');
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
            'reservation' => $reservation,
            'relatedReservations' => $relatedReservations,
            'userEmail' => Auth::user()->email,
        ]);
    }

    /**
     * Check and release expired reservations
     * This could be called via a scheduled task
     */
    public function releaseExpiredReservations()
    {
        $expiredReservations = Reservation::where('reserved_until', '<', now())
            ->whereNull('deleted_at')
            ->get();

        foreach ($expiredReservations as $reservation) {
            $reservation->delete();
        }

        return response()->json([
            'message' => count($expiredReservations) . ' expired reservations released.',
        ]);
    }
}
