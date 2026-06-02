<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    // Productos típicos de supermercado chileno
    private array $products = [
        ['Leche entera 1L', 1200],
        ['Pan de molde', 2500],
        ['Arroz 1kg', 1800],
        ['Aceite vegetal 1L', 2200],
        ['Pollo entero', 4500],
        ['Detergente líquido', 3800],
        ['Yogur natural', 900],
        ['Queso gauda 500g', 3200],
        ['Fideos espagueti', 1100],
        ['Café instantáneo', 4200],
        ['Papel higiénico x6', 3500],
        ['Tomates 1kg', 1500],
        ['Cebolla 1kg', 800],
        ['Jugo néctar', 1200],
        ['Shampoo', 2800],
        ['Mantequilla 200g', 2100],
        ['Huevos x12', 3600],
        ['Azúcar 1kg', 1400],
        ['Sal 1kg', 600],
        ['Jabón de manos', 1300],
    ];

    public function definition(): array
    {
        [$name, $price] = fake()->randomElement($this->products);

        return [
            'list_id'  => ShoppingList::factory(),
            'added_by' => User::factory(),
            'name'     => $name,
            'qty'      => fake()->randomElement([1, 1, 1, 2, 2, 3]),
            'price'    => $price + fake()->numberBetween(-200, 200),
            'checked'  => false,
        ];
    }

    /** Ítem ya marcado en el carrito */
    public function checked(): static
    {
        return $this->state(fn() => [
            'checked'    => true,
            'checked_at' => fake()->dateTimeBetween('-1 hour', 'now'),
        ]);
    }
}
