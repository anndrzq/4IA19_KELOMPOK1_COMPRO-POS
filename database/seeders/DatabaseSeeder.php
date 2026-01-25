<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        User::create([
            'name' => 'Super Admin',
            'phoneNumber' => '081212131060',
            'email' => 'superadmin@gmail.com',
            'address' => "Jalan Jalan",
            'password' => Hash::make('123123'),
            'role' => 'SuperAdmin',
            'gender' => 'Laki',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::create([
            'name' => 'Kasir',
            'phoneNumber' => '08111720050',
            'email' => 'kasir@gmail.com',
            'address' => "Jalan Jalan",
            'password' => Hash::make('123123'),
            'role' => 'Kasir',
            'gender' => 'Laki',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
