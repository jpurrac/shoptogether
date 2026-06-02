<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShoppingList extends Model
{
    use HasFactory;

    protected $table = 'lists';

    protected $dateFormat = 'U';

    protected $fillable = [
        'owner_id',
        'name',
        'budget',
        'code',
        'status',
        'closed_at',
    ];

    protected $casts = [
        'budget'     => 'integer',
        'closed_at'  => 'integer',
        'created_at' => 'integer',
        'updated_at' => 'integer',
    ];

    // ── Boot: generar código único al crear ───────────────────

    protected static function booted(): void
    {
        static::creating(function (ShoppingList $list) {
            $list->code = static::generateUniqueCode();

            // El dueño se agrega automáticamente como miembro 'owner'
            static::created(function (ShoppingList $list) {
                $list->members()->attach($list->owner_id, [
                    'role'      => 'owner',
                    'joined_at' => now()->timestamp,
                ]);
            });
        });
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(5));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    // ── Relaciones ──────────────────────────────────────────

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /** Todos los miembros (pivot con rol) */
    public function members()
    {
        return $this->belongsToMany(User::class, 'list_members', 'list_id', 'user_id')
                    ->withPivot('role', 'joined_at');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'list_id');
    }

    public function closure()
    {
        return $this->hasOne(ListClosure::class, 'list_id');
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // ── Helpers ──────────────────────────────────────────────

    public function isOwner(User $user): bool
    {
        return $this->owner_id === $user->id;
    }

    public function hasMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    /** Total de ítems marcados en carrito */
    public function getCartTotalAttribute(): int
    {
        return $this->items
            ->where('checked', true)
            ->sum(fn($item) => $item->price * $item->qty);
    }

    /** Número de ítems chequeados */
    public function getCheckedCountAttribute(): int
    {
        return $this->items->where('checked', true)->count();
    }
}
