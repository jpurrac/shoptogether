<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ListClosure;
use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShoppingListSeeder extends Seeder
{
    public function run(): void
    {
        $jp    = User::where('email', 'jp@shoptogether.test')->first();
        $ana   = User::where('email', 'ana@shoptogether.test')->first();
        $carlos = User::where('email', 'carlos@shoptogether.test')->first();

        // ── Lista 1: activa, compartida con Ana ───────────────────────────
        $lista1 = ShoppingList::create([
            'owner_id' => $jp->id,
            'name'     => 'Supermercado semana',
            'budget'   => 50000,
            'code'     => 'SHOP1',
            'status'   => 'active',
        ]);
        $lista1->members()->attach($jp->id,  ['role' => 'owner',  'joined_at' => now()]);
        $lista1->members()->attach($ana->id, ['role' => 'editor', 'joined_at' => now()]);

        $this->seedItems($lista1, $jp, $ana, 8, 5); // 8 pendientes, 5 en carrito

        // ── Lista 2: activa, solo JP ──────────────────────────────────────
        $lista2 = ShoppingList::create([
            'owner_id' => $jp->id,
            'name'     => 'Asado fin de semana',
            'budget'   => null,
            'code'     => 'ASADO',
            'status'   => 'active',
        ]);
        $lista2->members()->attach($jp->id, ['role' => 'owner', 'joined_at' => now()]);

        $this->seedItems($lista2, $jp, $jp, 4, 0);

        // ── Lista 3: pagada (historial) ───────────────────────────────────
        $lista3 = ShoppingList::create([
            'owner_id' => $jp->id,
            'name'     => 'Compras mes pasado',
            'budget'   => 80000,
            'code'     => 'HIST1',
            'status'   => 'paid',
            'closed_at' => now()->subDays(12),
        ]);
        $lista3->members()->attach($jp->id,    ['role' => 'owner',  'joined_at' => now()->subDays(15)]);
        $lista3->members()->attach($carlos->id,['role' => 'editor', 'joined_at' => now()->subDays(15)]);

        $this->seedItems($lista3, $jp, $carlos, 0, 10);

        // Closure de lista 3
        $cartTotal = $lista3->items()->sum(\DB::raw('price * qty'));
        ListClosure::create([
            'list_id'        => $lista3->id,
            'paid_by'        => $jp->id,
            'cart_total'     => $cartTotal,
            'total_real'     => $cartTotal + 1200,
            'payment_method' => 'debito',
            'comment'        => 'Se compró también una bolsa reutilizable',
        ]);

        // ── Lista 4: activa de Ana (JP unido) ─────────────────────────────
        $lista4 = ShoppingList::create([
            'owner_id' => $ana->id,
            'name'     => 'Farmacia',
            'budget'   => 25000,
            'code'     => 'FARMA',
            'status'   => 'active',
        ]);
        $lista4->members()->attach($ana->id,['role' => 'owner',  'joined_at' => now()]);
        $lista4->members()->attach($jp->id, ['role' => 'editor', 'joined_at' => now()]);

        $this->seedItems($lista4, $ana, $jp, 3, 2);
    }

    private function seedItems(
        ShoppingList $list,
        User $user1,
        User $user2,
        int $pending,
        int $checked
    ): void {
        $products = [
            ['Leche entera 1L', 1200], ['Pan de molde', 2500],
            ['Arroz 1kg', 1800],       ['Aceite vegetal', 2200],
            ['Pollo entero', 4500],    ['Detergente', 3800],
            ['Yogur natural', 900],    ['Queso gauda', 3200],
            ['Fideos', 1100],          ['Café', 4200],
            ['Papel higiénico x6', 3500], ['Tomates 1kg', 1500],
            ['Cebolla 1kg', 800],      ['Jugo néctar', 1200],
            ['Shampoo', 2800],         ['Mantequilla', 2100],
            ['Huevos x12', 3600],      ['Azúcar 1kg', 1400],
        ];

        shuffle($products);
        $idx = 0;

        // Pendientes (no chequeados)
        for ($i = 0; $i < $pending; $i++) {
            [$name, $price] = $products[$idx++ % count($products)];
            Item::create([
                'list_id'  => $list->id,
                'added_by' => ($i % 2 === 0) ? $user1->id : $user2->id,
                'name'     => $name,
                'qty'      => rand(1, 3),
                'price'    => $price,
                'checked'  => false,
            ]);
        }

        // En carrito (chequeados)
        for ($i = 0; $i < $checked; $i++) {
            [$name, $price] = $products[$idx++ % count($products)];
            Item::create([
                'list_id'    => $list->id,
                'added_by'   => ($i % 2 === 0) ? $user2->id : $user1->id,
                'name'       => $name,
                'qty'        => rand(1, 2),
                'price'      => $price,
                'checked'    => true,
                'checked_at' => now()->subMinutes(rand(5, 120)),
            ]);
        }
    }
}
