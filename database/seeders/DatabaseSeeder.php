<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        User::create([
            'name'          => 'Ananda Rizq',
            'email'         => 'anndrzq32@gmail.com',
            'phoneNumber'   => '081212131060',
            'password'      => bcrypt('123123'),
            'role'          => 'SuperAdmin',
            'jk'            => 'Laki'
        ]);
    }
}
