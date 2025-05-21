<?php

namespace Database\Seeders;

use App\Models\Concert;
use Illuminate\Database\Seeder;

class ConcertSeeder extends Seeder
{
    public function run()
    {
        Concert::create([
            'artist' => 'The Baltic Beats',
            'location_id' => 1, // Riga Concert Hall
        ]);

        Concert::create([
            'artist' => 'Liepaja Symphony',
            'location_id' => 2, // Liepaja Theater
        ]);

        Concert::create([
            'artist' => 'Daugavpils Rockers',
            'location_id' => 3, // Daugavpils Arena
        ]);
    }
}
