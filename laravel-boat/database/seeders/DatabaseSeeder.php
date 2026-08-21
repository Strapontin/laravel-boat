<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Boat;
use App\Models\Reservation;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin'),
            'is_admin' => true,
        ]);

        $user = User::factory()->create([
            'name' => 'user',
            'email' => 'user@user.com',
            'password' => bcrypt('user'),
        ]);

        Boat::create(['color' => 'Rouge', 'position' => 0]);
        Boat::create(['color' => 'Jaune', 'position' => 1]);
        Boat::create(['color' => 'Orange', 'position' => 2]);
        Boat::create(['color' => 'Vert', 'position' => 3]);
        Boat::create(['color' => 'Bleu', 'position' => 4]);
    }
}
