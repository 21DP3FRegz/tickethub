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
            'seat_id' => 'required|exists:seats,id',
        ]);

        $show = Show::findOrFail($request->show_id);
        $seat = Seat::findOrFail($request->seat_id);

        // Check if seat is available
        if ($seat->reservation || $seat->ticket) {
            return back()->withErrors(['seat' => 'This seat is already reserved or booked.']);
        }

        // Create reservation with 15-minute duration
        $reservation = Reservation::create([
            'show_id' => $show->id,
            'seat_id' => $seat->id,
            'reservation_token' => Str::random(32),
            'reserved_until' => now()->addMinutes(15),
            'user_id' => Auth::id(),
        ]);

        \App\Jobs\ReleaseReservationJob::dispatch($reservation->id)
            ->delay($reservation->reserved_until);

        return redirect()->route('reservations.createBooking', $reservation)
            ->with('success', 'Seat reserved for 15 minutes. Please complete your booking.');
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

        return Inertia::render('bookings/Create', [
            'reservation' => $reservation,
            'userEmail' => Auth::user()->email,
        ]);
    }
}
