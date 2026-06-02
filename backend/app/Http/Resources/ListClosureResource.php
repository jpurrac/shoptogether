<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListClosureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (! $this->resource) {
            return [];
        }

        return [
            'id'             => $this->id,
            'cart_total'     => $this->cart_total,
            'total_real'     => $this->total_real,
            'payment_method' => $this->payment_method,
            'comment'        => $this->comment,
            'paid_by'        => new UserResource($this->whenLoaded('paidBy')),
            'paid_at'        => $this->paid_at,
        ];
    }
}
