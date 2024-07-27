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
            'uuid' => Str::uuid(),
            'name' => 'Ananda Rizq',
            'phoneNumber' => '081212131060',
            'email' => 'anndrzq32@gmail.com',
            'address' => "Jalan Jalan",
            'password' => Hash::make('123123'),
            'role' => 'SuperAdmin',
            'jk' => 'Laki',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
