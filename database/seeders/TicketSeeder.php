<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Show;
use App\Models\Ticket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TicketSeeder extends Seeder
{
    public function run()
    {
        $bookings = Booking::all();

        // Generate some pre-bought tickets for the first few shows
        $shows = Show::take(5)->get(); // First 5 shows

        foreach ($shows as $show) {
            $seats = $show->seats()->inRandomOrder()->take(rand(10, 20))->get();

            foreach ($seats as $seat) {
                $booking = $bookings->random();

                Ticket::create([
                    'booking_id' => $booking->id,
                    'show_id' => $show->id,
                    'seat_id' => $seat->id,
                    'code' => strtoupper(Str::random(8)),
                    'name' => $booking->name,
                ]);
            }
        }
    }
}
