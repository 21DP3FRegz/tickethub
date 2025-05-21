<?php

namespace Database\Seeders;

use App\Models\Row;
use App\Models\Seat;
use Illuminate\Database\Seeder;

class SeatSeeder extends Seeder
{
    public function run()
    {
        // Fetch all rows
        $rows = Row::all();

        foreach ($rows as $row) {
            // Create seats based on total_seats
            for ($i = 1; $i <= $row->total_seats; $i++) {
                Seat::create([
                    'show_id' => $row->show_id,
                    'row_id' => $row->id,
                    'seat_number' => $i,
                    'label' => $row->name . $i, // e.g., "A1", "B2"
                ]);
            }
        }
    }
}
