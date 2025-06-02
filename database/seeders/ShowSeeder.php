<?php

namespace Database\Seeders;

use App\Models\Show;
use Illuminate\Database\Seeder;

class ShowSeeder extends Seeder
{
    public function run()
    {
        // Prāta Vētra - 3 shows
        Show::create([
            'concert_id' => 1,
            'start' => '2025-06-15 20:00:00',
            'end' => '2025-06-15 22:30:00',
        ]);

        Show::create([
            'concert_id' => 1,
            'start' => '2025-06-16 20:00:00',
            'end' => '2025-06-16 22:30:00',
        ]);

        Show::create([
            'concert_id' => 1,
            'start' => '2025-06-17 20:00:00',
            'end' => '2025-06-17 22:30:00',
        ]);

        // Latvijas Radio koris - 2 shows
        Show::create([
            'concert_id' => 2,
            'start' => '2025-07-01 19:00:00',
            'end' => '2025-07-01 21:00:00',
        ]);

        Show::create([
            'concert_id' => 2,
            'start' => '2025-07-02 19:00:00',
            'end' => '2025-07-02 21:00:00',
        ]);

        // Dons - 2 shows
        Show::create([
            'concert_id' => 3,
            'start' => '2025-07-10 19:30:00',
            'end' => '2025-07-10 21:30:00',
        ]);

        Show::create([
            'concert_id' => 3,
            'start' => '2025-07-11 19:30:00',
            'end' => '2025-07-11 21:30:00',
        ]);

        // Instrumenti - 1 show
        Show::create([
            'concert_id' => 4,
            'start' => '2025-07-20 20:00:00',
            'end' => '2025-07-20 22:00:00',
        ]);

        // Liepājas Simfoniskais orķestris - 1 show
        Show::create([
            'concert_id' => 5,
            'start' => '2025-08-15 18:30:00',
            'end' => '2025-08-15 20:30:00',
        ]);
    }
}
