<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $customerRole = Role::where('name', 'customer')->first();
        $adminRole = Role::where('name', 'administrator')->first();

        // Customers
        $customer1 = User::create([
            'name' => 'Jānis Bērziņš',
            'email' => 'janis.berzins@epasts.lv',
            'password' => bcrypt('password'),
        ]);
        $customer1->roles()->attach($customerRole);

        $customer2 = User::create([
            'name' => 'Anna Kalniņa',
            'email' => 'anna.kalnina@inbox.lv',
            'password' => bcrypt('password'),
        ]);
        $customer2->roles()->attach($customerRole);

        $customer3 = User::create([
            'name' => 'Pēteris Ozoliņš',
            'email' => 'peteris.ozolins@apollo.lv',
            'password' => bcrypt('password'),
        ]);
        $customer3->roles()->attach($customerRole);

        $customer4 = User::create([
            'name' => 'Līga Liepiņa',
            'email' => 'liga.liepina@gmail.com',
            'password' => bcrypt('password'),
        ]);
        $customer4->roles()->attach($customerRole);

        $customer5 = User::create([
            'name' => 'Māris Kalējs',
            'email' => 'maris.kalejs@one.lv',
            'password' => bcrypt('password'),
        ]);
        $customer5->roles()->attach($customerRole);

        $customer6 = User::create([
            'name' => 'Ilze Vītola',
            'email' => 'ilze.vitola@inbox.lv',
            'password' => bcrypt('password'),
        ]);
        $customer6->roles()->attach($customerRole);

        $customer7 = User::create([
            'name' => 'Kārlis Jansons',
            'email' => 'karlis.jansons@apollo.lv',
            'password' => bcrypt('password'),
        ]);
        $customer7->roles()->attach($customerRole);

        // Admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $admin->roles()->attach($adminRole);
    }
}
