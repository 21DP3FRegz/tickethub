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

        $customer = User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
        ]);
        $customer->roles()->attach($customerRole);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $admin->roles()->attach($adminRole);
    }
}
