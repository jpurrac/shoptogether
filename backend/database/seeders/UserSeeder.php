<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario de prueba fijo (para desarrollo)
        User::firstOrCreate(
            ['email' => 'jp@shoptogether.test'],
            [
                'name'              => 'JP Urra',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'ana@shoptogether.test'],
            [
                'name'              => 'Ana García',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'carlos@shoptogether.test'],
            [
                'name'              => 'Carlos Rojas',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Usuarios extra aleatorios
        User::factory(7)->create();
    }
}
