<?php

namespace App\Providers;

use App\Models\ShoppingList;
use App\Policies\ListPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        ShoppingList::class => ListPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
