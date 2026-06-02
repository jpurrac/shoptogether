<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $dateFormat = 'U';

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google_id',
    ];

    protected $casts = [
        'password'   => 'hashed',
        'created_at' => 'integer',
        'updated_at' => 'integer',
    ];

    // ── Relaciones ───────────────────────────────────────────

    /** Listas que el usuario creó */
    public function ownedLists()
    {
        return $this->hasMany(ShoppingList::class, 'owner_id');
    }

    /** Listas en las que participa (incluye las propias) */
    public function lists()
    {
        return $this->belongsToMany(ShoppingList::class, 'list_members', 'user_id', 'list_id')
                    ->withPivot('role', 'joined_at');
    }

    /** Ítems que agregó */
    public function items()
    {
        return $this->hasMany(Item::class, 'added_by');
    }

    // ── Helpers ──────────────────────────────────────────────

    /** ¿Tiene contraseña? (false = solo OAuth) */
    public function hasPassword(): bool
    {
        return $this->password !== null;
    }

    // ── Accessors ────────────────────────────────────────────

    /** Iniciales para el avatar → "Juan Pérez" = "JP" */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        return strtoupper(
            collect($words)->take(2)->map(fn($w) => $w[0] ?? '')->implode('')
        );
    }

    /** Color determinista basado en el id */
    public function getColorAttribute(): string
    {
        $colors = [
            'linear-gradient(135deg,#1565C0,#00ACC1)',
            'linear-gradient(135deg,#F57C00,#FF9800)',
            'linear-gradient(135deg,#43A047,#66BB6A)',
            'linear-gradient(135deg,#8E24AA,#AB47BC)',
            'linear-gradient(135deg,#E53935,#EF5350)',
        ];
        return $colors[$this->id % count($colors)];
    }
}