<?php

namespace Database\Seeders;

use App\Models\Row;
use Illuminate\Database\Seeder;

class RowSeeder extends Seeder
{
    public function run()
    {
        // Configuration for rows per show
        $showRows = [
            1 => [
                ['name' => 'A', 'order' => 1, 'total_seats' => 10], // Front Row
                ['name' => 'B', 'order' => 2, 'total_seats' => 12], // Middle Row
                ['name' => 'C', 'order' => 3, 'total_seats' => 10], // Back Row
            ],
            2 => [
                ['name' => 'A', 'order' => 1, 'total_seats' => 8],  // Front Row
                ['name' => 'B', 'order' => 2, 'total_seats' => 10], // Middle Row
            ],
            3 => [
                ['name' => 'A', 'order' => 1, 'total_seats' => 8],  // Balcony
                ['name' => 'B', 'order' => 2, 'total_seats' => 6],  // Upper Balcony
            ],
            4 => [
                ['name' => 'A', 'order' => 1, 'total_seats' => 12], // Main Floor
                ['name' => 'B', 'order' => 2, 'total_seats' => 10], // Rear Floor
            ],
        ];

        foreach ($showRows as $showId => $rows) {
            foreach ($rows as $row) {
                Row::create([
                    'show_id' => $showId,
                    'name' => $row['name'],
                    'order' => $row['order'],
                    'total_seats' => $row['total_seats'],
                ]);
            }
        }
    }
}
