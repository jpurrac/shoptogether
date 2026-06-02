<?php

namespace App\Events;

use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Models\ShoppingList;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ListChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $type;
    public array  $payload;
    public int    $listId;

    /**
     * @param string       $type    'item_created' | 'item_updated' | 'item_deleted' | 'item_toggled'
     * @param int          $listId
     * @param array        $payload
     */
    public function __construct(string $type, int $listId, array $payload = [])
    {
        $this->type    = $type;
        $this->listId  = $listId;
        $this->payload = $payload;
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel("list.{$this->listId}");
    }

    public function broadcastAs(): string
    {
        return 'ListChanged';
    }

    public function broadcastWith(): array
    {
        return [
            'type'    => $this->type,
            'list_id' => $this->listId,
            'payload' => $this->payload,
        ];
    }
}
