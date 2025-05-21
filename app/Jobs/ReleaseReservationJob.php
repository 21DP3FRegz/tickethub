<?php

namespace App\Jobs;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReleaseReservationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reservationId;

    public function __construct($reservationId)
    {
        $this->reservationId = $reservationId;
    }

    public function handle()
    {
        $reservation = Reservation::find($this->reservationId);
        if ($reservation && $reservation->reserved_until < now()) {
            $reservation->delete();
        }
    }
}
