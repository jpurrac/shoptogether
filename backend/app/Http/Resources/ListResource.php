<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'budget'        => $this->budget,
            'code'          => $this->code,
            'status'        => $this->status,
            'closed_at'     => $this->closed_at,

            // Items completos (para DetailView) y contadores (para ListView)
            'items'         => ItemResource::collection($this->whenLoaded('items')),
            'item_count'    => $this->whenLoaded('items', fn() => $this->items->count()),
            'checked_count' => $this->whenLoaded('items', fn() => $this->checked_count),
            'cart_total'    => $this->whenLoaded('items', fn() => $this->cart_total),

            // Miembros
            'members'       => UserResource::collection($this->whenLoaded('members')),
            'owner'         => new UserResource($this->whenLoaded('owner')),

            // ¿La lista es compartida? (más de 1 miembro)
            'shared'        => $this->whenLoaded('members', fn() => $this->members->count() > 1),

            // Detalle de cierre
            'closure'       => new ListClosureResource($this->whenLoaded('closure')),

            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
