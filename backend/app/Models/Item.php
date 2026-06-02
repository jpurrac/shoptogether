<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'list_id',
        'added_by',
        'name',
        'qty',
        'price',
        'checked',
        'checked_at',
    ];

    protected $dateFormat = 'U';

    protected $casts = [
        'qty'        => 'integer',
        'price'      => 'integer',
        'checked'    => 'boolean',
        'checked_at' => 'integer',
        'created_at' => 'integer',
        'updated_at' => 'integer',
    ];

    // ── Relaciones ──────────────────────────────────────────

    public function list()
    {
        return $this->belongsTo(ShoppingList::class, 'list_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    // ── Helpers ──────────────────────────────────────────────

    public function getTotalAttribute(): int
    {
        return $this->price * $this->qty;
    }

    /** Marcar/desmarcar en carrito */
    public function toggle(): void
    {
        $this->checked    = !$this->checked;
        $this->checked_at = $this->checked ? now()->timestamp : null;
        $this->save();
    }
}
