<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Reservation;
use App\Models\Ticket;
use App\Notifications\BookingConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Auth::user()->bookings()
            ->with(['tickets.show.concert', 'tickets.seat.row'])
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('bookings/Index', [
            'bookings' => $bookings,
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
        ]);

        $reservation = Reservation::with(['show.concert', 'seat'])
            ->findOrFail($request->reservation_id);

        // Check if the reservation belongs to the user
        if ($reservation->user_id !== Auth::id()) {
            return redirect()->route('reservations.index')
                ->with('error', 'This reservation does not belong to you.');
        }

        // Check if the reservation is still valid
        if ($reservation->reserved_until < now()) {
            return redirect()->route('reservations.index')
                ->with('error', 'This reservation has expired.');
        }

        // Get the authenticated user's email
        $userEmail = Auth::user()->email;

        return Inertia::render('bookings/Create', [
            'reservation' => $reservation,
            'userEmail' => $userEmail,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zip' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $reservation = Reservation::findOrFail($validated['reservation_id']);

        if ($reservation->user_id !== Auth::id() || $reservation->reserved_until < now()) {
            return back()->withErrors(['reservation' => 'Invalid or expired reservation.']);
        }

        $booking = Booking::create([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'zip' => $validated['zip'],
            'country' => $validated['country'],
            'email' => $validated['email'],
            'user_id' => Auth::id(),
        ]);

        Ticket::create([
            'booking_id' => $booking->id,
            'show_id' => $reservation->show_id,
            'seat_id' => $reservation->seat_id,
            'code' => strtoupper(Str::random(8)),
            'name' => $validated['name'],
        ]);

        $reservation->delete();

        return redirect()->route('bookings.index')
            ->with('success', 'Booking confirmed!');
    }

    public function show(Booking $booking)
    {
        // Authorize that the user can view this booking
        if (Gate::denies('view', $booking)) {
            abort(403, 'Unauthorized action.');
        }

        // Load related data
        $booking->load(['tickets.show.concert', 'tickets.seat.row']);

        return Inertia::render('bookings/Show', [
            'booking' => $booking,
        ]);
    }

    public function destroy(Booking $booking)
    {
        if (Gate::denies('delete', $booking)) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the booking can be cancelled (e.g., not too close to event date)
        $canCancel = true;
        foreach ($booking->tickets as $ticket) {
            // Example: Can't cancel within 24 hours of the show
            if ($ticket->show->start < now()->addHours(24)) {
                $canCancel = false;
                break;
            }
        }

        if (!$canCancel) {
            return back()->withErrors(['cancel' => 'Cannot cancel bookings less than 24 hours before the show.']);
        }

        // Delete tickets first, then the booking
        $booking->tickets()->delete();
        $booking->delete();

        return redirect()->route('bookings.index')
            ->with('success', 'Booking has been cancelled successfully.');
    }
}
