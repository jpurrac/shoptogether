<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => Hash::make('password'), // password por defecto en tests
            'remember_token'    => Str::random(10),
            'avatar'            => null,
            'google_id'         => null,
        ];
    }

    /** Usuario sin contraseña (solo OAuth) */
    public function googleUser(): static
    {
        return $this->state(fn() => [
            'password'  => null,
            'google_id' => fake()->numerify('####################'),
        ]);
    }

    /** Email no verificado */
    public function unverified(): static
    {
        return $this->state(fn() => [
            'email_verified_at' => null,
        ]);
    }
}
