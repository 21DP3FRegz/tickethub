<?php

namespace Database\Seeders;

use App\Models\Row;
use Illuminate\Database\Seeder;

class RowSeeder extends Seeder
{
    public function run()
    {
        // Configuration for rows per show with varied seating arrangements
        $showRows = [
            // Show 1: Prāta Vētra at Arena Riga - First night
            1 => [
                ['name' => 'VIP', 'order' => 1, 'total_seats' => 20],
                ['name' => 'A', 'order' => 2, 'total_seats' => 25],
                ['name' => 'B', 'order' => 3, 'total_seats' => 30],
                ['name' => 'C', 'order' => 4, 'total_seats' => 35],
                ['name' => 'D', 'order' => 5, 'total_seats' => 40],
                ['name' => 'E', 'order' => 6, 'total_seats' => 45],
                ['name' => 'F', 'order' => 7, 'total_seats' => 50],
            ],
            // Show 2: Prāta Vētra - Second night
            2 => [
                ['name' => 'VIP', 'order' => 1, 'total_seats' => 20],
                ['name' => 'A', 'order' => 2, 'total_seats' => 25],
                ['name' => 'B', 'order' => 3, 'total_seats' => 30],
                ['name' => 'C', 'order' => 4, 'total_seats' => 35],
                ['name' => 'D', 'order' => 5, 'total_seats' => 40],
                ['name' => 'E', 'order' => 6, 'total_seats' => 45],
                ['name' => 'F', 'order' => 7, 'total_seats' => 50],
            ],
            // Show 3: Prāta Vētra - Third night
            3 => [
                ['name' => 'VIP', 'order' => 1, 'total_seats' => 20],
                ['name' => 'A', 'order' => 2, 'total_seats' => 25],
                ['name' => 'B', 'order' => 3, 'total_seats' => 30],
                ['name' => 'C', 'order' => 4, 'total_seats' => 35],
                ['name' => 'D', 'order' => 5, 'total_seats' => 40],
                ['name' => 'E', 'order' => 6, 'total_seats' => 45],
                ['name' => 'F', 'order' => 7, 'total_seats' => 50],
            ],
            // Show 4: Latvijas Radio koris - First night
            4 => [
                ['name' => 'Parterre', 'order' => 1, 'total_seats' => 18],
                ['name' => 'Loža A', 'order' => 2, 'total_seats' => 8],
                ['name' => 'Loža B', 'order' => 3, 'total_seats' => 8],
                ['name' => 'Balkonss A', 'order' => 4, 'total_seats' => 12],
                ['name' => 'Balkonss B', 'order' => 5, 'total_seats' => 12],
                ['name' => 'Balkonss C', 'order' => 6, 'total_seats' => 10],
                ['name' => 'Galerija', 'order' => 7, 'total_seats' => 15],
            ],
            // Show 5: Latvijas Radio koris - Second night
            5 => [
                ['name' => 'Parterre', 'order' => 1, 'total_seats' => 18],
                ['name' => 'Loža A', 'order' => 2, 'total_seats' => 8],
                ['name' => 'Loža B', 'order' => 3, 'total_seats' => 8],
                ['name' => 'Balkonss A', 'order' => 4, 'total_seats' => 12],
                ['name' => 'Balkonss B', 'order' => 5, 'total_seats' => 12],
                ['name' => 'Balkonss C', 'order' => 6, 'total_seats' => 10],
                ['name' => 'Galerija', 'order' => 7, 'total_seats' => 15],
            ],
            // Show 6: Dons - First night
            6 => [
                ['name' => 'A', 'order' => 1, 'total_seats' => 16],
                ['name' => 'B', 'order' => 2, 'total_seats' => 18],
                ['name' => 'C', 'order' => 3, 'total_seats' => 20],
                ['name' => 'D', 'order' => 4, 'total_seats' => 22],
                ['name' => 'E', 'order' => 5, 'total_seats' => 20],
                ['name' => 'F', 'order' => 6, 'total_seats' => 18],
                ['name' => 'G', 'order' => 7, 'total_seats' => 16],
            ],
            // Show 7: Dons - Second night
            7 => [
                ['name' => 'A', 'order' => 1, 'total_seats' => 16],
                ['name' => 'B', 'order' => 2, 'total_seats' => 18],
                ['name' => 'C', 'order' => 3, 'total_seats' => 20],
                ['name' => 'D', 'order' => 4, 'total_seats' => 22],
                ['name' => 'E', 'order' => 5, 'total_seats' => 20],
                ['name' => 'F', 'order' => 6, 'total_seats' => 18],
                ['name' => 'G', 'order' => 7, 'total_seats' => 16],
            ],
            // Show 8: Instrumenti
            8 => [
                ['name' => 'Priekšējā', 'order' => 1, 'total_seats' => 14],
                ['name' => 'A', 'order' => 2, 'total_seats' => 16],
                ['name' => 'B', 'order' => 3, 'total_seats' => 18],
                ['name' => 'C', 'order' => 4, 'total_seats' => 20],
                ['name' => 'D', 'order' => 5, 'total_seats' => 18],
                ['name' => 'E', 'order' => 6, 'total_seats' => 16],
                ['name' => 'Aizmugurējā', 'order' => 7, 'total_seats' => 14],
            ],
            // Show 9: Liepājas Simfoniskais orķestris
            9 => [
                ['name' => 'Premium', 'order' => 1, 'total_seats' => 10],
                ['name' => 'A', 'order' => 2, 'total_seats' => 14],
                ['name' => 'B', 'order' => 3, 'total_seats' => 16],
                ['name' => 'C', 'order' => 4, 'total_seats' => 18],
                ['name' => 'D', 'order' => 5, 'total_seats' => 16],
                ['name' => 'E', 'order' => 6, 'total_seats' => 14],
                ['name' => 'Standarta', 'order' => 7, 'total_seats' => 12],
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
