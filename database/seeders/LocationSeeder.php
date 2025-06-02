<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run()
    {
        Location::create(['name' => 'Latvijas Nacionālā opera un balets']); // Riga
        Location::create(['name' => 'Dailes teātris']); // Riga
        Location::create(['name' => 'Liepājas koncertzāle "Lielais dzintars"']); // Liepaja
        Location::create(['name' => 'Ventspils koncertzāle "Latvija"']); // Ventspils
        Location::create(['name' => 'Daugavpils Kultūras pils']); // Daugavpils
        Location::create(['name' => 'Jelgavas kultūras nams']); // Jelgava
        Location::create(['name' => 'Valmiera Drama Theatre']); // Valmiera
        Location::create(['name' => 'Arena Riga']); // Riga - for larger concerts
    }
}
