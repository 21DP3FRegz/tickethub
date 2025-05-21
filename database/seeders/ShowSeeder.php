<?php

namespace Database\Seeders;

use App\Models\Show;
use Illuminate\Database\Seeder;

class ShowSeeder extends Seeder
{
    public function run()
    {
        Show::create([
            'concert_id' => 1,
            'start' => '2025-06-01 19:00:00',
            'end' => '2025-06-01 21:00:00',
        ]);

        Show::create([
            'concert_id' => 1,
            'start' => '2025-06-02 18:00:00',
            'end' => '2025-06-02 20:00:00',
        ]);

        Show::create([
            'concert_id' => 2,
            'start' => '2025-06-15 20:00:00',
            'end' => '2025-06-15 22:00:00',
        ]);

        Show::create([
            'concert_id' => 3,
            'start' => '2025-06-20 19:30:00',
            'end' => '2025-06-20 21:30:00',
        ]);
    }
}
