<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Concert;
use Illuminate\Database\Seeder;

class ConcertSeeder extends Seeder
{
    public function run()
    {
        $prataVetra = Artist::where('name', 'Prāta Vētra (Brainstorm)')->first();
        $radioKoris = Artist::where('name', 'Latvijas Radio koris')->first();
        $dons = Artist::where('name', 'Dons')->first();
        $instrumenti = Artist::where('name', 'Instrumenti')->first();
        $lso = Artist::where('name', 'Liepājas Simfoniskais orķestris')->first();

        Concert::create([
            'artist_id' => $prataVetra->id,
            'location_id' => 8, // Arena Riga
        ]);

        Concert::create([
            'artist_id' => $radioKoris->id,
            'location_id' => 1, // Latvijas Nacionālā opera un balets
        ]);

        Concert::create([
            'artist_id' => $dons->id,
            'location_id' => 2, // Dailes teātris
        ]);

        Concert::create([
            'artist_id' => $instrumenti->id,
            'location_id' => 3, // Liepājas koncertzāle
        ]);

        Concert::create([
            'artist_id' => $lso->id,
            'location_id' => 3, // Liepājas koncertzāle
        ]);
    }
}
