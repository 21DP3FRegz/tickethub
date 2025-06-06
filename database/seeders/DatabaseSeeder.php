<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LocationSeeder::class,
            ArtistSeeder::class,
            ConcertSeeder::class,
            ShowSeeder::class,
            RowSeeder::class,
            SeatSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            BookingSeeder::class,
            TicketSeeder::class,
        ]);
    }
}
