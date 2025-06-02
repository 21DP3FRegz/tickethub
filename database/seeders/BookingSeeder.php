<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run()
    {
        $users = User::where('name', '!=', 'Admin User')->get();

        Booking::create([
            'name' => 'Jānis Bērziņš',
            'address' => 'Brīvības iela 123',
            'city' => 'Rīga',
            'zip' => 'LV-1001',
            'country' => 'Latvija',
            'email' => 'janis.berzins@epasts.lv',
            'user_id' => $users->where('name', 'Jānis Bērziņš')->first()->id,
        ]);

        Booking::create([
            'name' => 'Anna Kalniņa',
            'address' => 'Elizabetes iela 45',
            'city' => 'Rīga',
            'zip' => 'LV-1010',
            'country' => 'Latvija',
            'email' => 'anna.kalnina@inbox.lv',
            'user_id' => $users->where('name', 'Anna Kalniņa')->first()->id,
        ]);

        Booking::create([
            'name' => 'Pēteris Ozoliņš',
            'address' => 'Liepājas iela 67',
            'city' => 'Jelgava',
            'zip' => 'LV-3001',
            'country' => 'Latvija',
            'email' => 'peteris.ozolins@apollo.lv',
            'user_id' => $users->where('name', 'Pēteris Ozoliņš')->first()->id,
        ]);

        Booking::create([
            'name' => 'Līga Liepiņa',
            'address' => 'Vienības gatve 89',
            'city' => 'Rīga',
            'zip' => 'LV-1004',
            'country' => 'Latvija',
            'email' => 'liga.liepina@gmail.com',
            'user_id' => $users->where('name', 'Līga Liepiņa')->first()->id,
        ]);

        Booking::create([
            'name' => 'Māris Kalējs',
            'address' => 'Daugavpils iela 12',
            'city' => 'Daugavpils',
            'zip' => 'LV-5401',
            'country' => 'Latvija',
            'email' => 'maris.kalejs@one.lv',
            'user_id' => $users->where('name', 'Māris Kalējs')->first()->id,
        ]);

        Booking::create([
            'name' => 'Ilze Vītola',
            'address' => 'Ventspils iela 34',
            'city' => 'Ventspils',
            'zip' => 'LV-3601',
            'country' => 'Latvija',
            'email' => 'ilze.vitola@inbox.lv',
            'user_id' => $users->where('name', 'Ilze Vītola')->first()->id,
        ]);

        Booking::create([
            'name' => 'Kārlis Jansons',
            'address' => 'Zemgales iela 78',
            'city' => 'Rīga',
            'zip' => 'LV-1002',
            'country' => 'Latvija',
            'email' => 'karlis.jansons@apollo.lv',
            'user_id' => $users->where('name', 'Kārlis Jansons')->first()->id,
        ]);
    }
}
