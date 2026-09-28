<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // <-- PERBAIKAN: Import Model User
use Illuminate\Support\Facades\Hash; // <-- PERBAIKAN: Import Facade Hash

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Hapus user lama jika ada agar tidak bentrok dengan email unique
        User::where('email', 'admin@gmail.com')->delete();

        User::create([
            'name'     => 'Admin WNA',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        $this->call(ForeignerSeeder::class);
    }
}