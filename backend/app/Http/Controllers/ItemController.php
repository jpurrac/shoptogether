<?php

namespace App\Http\Controllers;

use App\Events\ListChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Models\ShoppingList;
use Illuminate\Http\JsonResponse;

class ItemController extends Controller
{
    public function index(ShoppingList $list): JsonResponse
    {
        $this->authorize('view', $list);

        $items = $list->items()
            ->with('addedBy')
            ->orderBy('checked')
            ->orderBy('created_at')
            ->get();

        return response()->json(ItemResource::collection($items));
    }

    public function store(StoreItemRequest $request, ShoppingList $list): JsonResponse
    {
        $this->authorize('manageItems', $list);

        $item = $list->items()->create([
            'added_by' => $request->user()->id,
            'name'     => $request->name,
            'qty'      => $request->qty ?? 1,
            'price'    => $request->price ?? 0,
        ]);

        $item->load('addedBy');
        $resource = new ItemResource($item);

        broadcast(new ListChanged('item_created', $list->id, $resource->resolve()));

        return response()->json($resource, 201);
    }

    public function update(UpdateItemRequest $request, Item $item): JsonResponse
    {
        $this->authorize('manageItems', $item->list);

        $item->update($request->only('name', 'qty', 'price'));
        $item->load('addedBy');
        $resource = new ItemResource($item);

        broadcast(new ListChanged('item_updated', $item->list_id, $resource->resolve()));

        return response()->json($resource);
    }

    public function destroy(Item $item): JsonResponse
    {
        $this->authorize('manageItems', $item->list);

        $listId = $item->list_id;
        $itemId = $item->id;
        $item->delete();

        broadcast(new ListChanged('item_deleted', $listId, ['id' => $itemId]));

        return response()->json(['message' => 'Producto eliminado.']);
    }

    public function toggle(Item $item): JsonResponse
    {
        $this->authorize('manageItems', $item->list);

        $item->toggle();
        $item->load('addedBy');
        $resource = new ItemResource($item);

        broadcast(new ListChanged('item_toggled', $item->list_id, $resource->resolve()));

        return response()->json($resource);
    }
}
