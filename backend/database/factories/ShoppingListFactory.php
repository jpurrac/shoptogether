<?php

namespace Database\Factories;

use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ShoppingListFactory extends Factory
{
    protected $model = ShoppingList::class;

    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'name'     => fake()->randomElement([
                'Supermercado semana',
                'Cumpleaños de ' . fake()->firstName(),
                'Asado del fin de semana',
                'Compras mes ' . fake()->monthName(),
                'Jumbo ' . fake()->dayOfWeek(),
            ]),
            'budget'   => fake()->boolean(60)
                ? fake()->numberBetween(10000, 150000)
                : null,
            'code'     => strtoupper(Str::random(5)),
            'status'   => 'active',
        ];
    }

    /** Lista ya cerrada/pagada */
    public function paid(): static
    {
        return $this->state(fn() => [
            'status'    => 'paid',
            'closed_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /** Lista sin presupuesto */
    public function noBudget(): static
    {
        return $this->state(fn() => ['budget' => null]);
    }
}
