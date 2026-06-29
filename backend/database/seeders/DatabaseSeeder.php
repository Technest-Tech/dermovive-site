<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Phase 0: a single admin account so the Filament panel is usable.
     * Phase 1 will add roles/permissions and the catalog demo seeders.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@dermovive.test'],
            [
                'name' => 'Dermovive Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
    }
}
