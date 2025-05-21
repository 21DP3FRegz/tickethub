<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    /**
     * Determine whether the user can view the booking.
     */
    public function view(User $user, Booking $booking): Response
    {
        return $user->id === $booking->user_id
            ? Response::allow()
            : Response::deny('You do not own this booking.');
    }

    /**
     * Determine whether the user can delete the booking.
     */
    public function delete(User $user, Booking $booking): Response
    {
        if ($user->id !== $booking->user_id) {
            return Response::deny('You do not own this booking.');
        }

        // Check if any ticket is for a show starting in less than 24 hours
        foreach ($booking->tickets as $ticket) {
            if ($ticket->show->start < now()->addHours(24)) {
                return Response::deny('Cannot cancel bookings less than 24 hours before the show.');
            }
        }

        return Response::allow();
    }
}
