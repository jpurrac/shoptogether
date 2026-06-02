<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListClosure extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'list_id',
        'paid_by',
        'cart_total',
        'total_real',
        'payment_method',
        'comment',
        'paid_at',
    ];

    protected $casts = [
        'cart_total' => 'integer',
        'total_real' => 'integer',
        'paid_at'    => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (ListClosure $closure) {
            $closure->paid_at = now()->timestamp;
        });
    }

    public function list()
    {
        return $this->belongsTo(ShoppingList::class, 'list_id');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
