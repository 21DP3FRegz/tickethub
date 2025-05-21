<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run()
    {
        Location::create(['name' => 'Riga Concert Hall']);
        Location::create(['name' => 'Liepaja Theater']);
        Location::create(['name' => 'Daugavpils Arena']);
    }
}
