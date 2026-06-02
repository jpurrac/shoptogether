<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreListRequest;
use App\Http\Requests\UpdateListRequest;
use App\Http\Requests\CloseListRequest;
use App\Http\Resources\ListResource;
use App\Models\ListClosure;
use App\Models\ShoppingList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListController extends Controller
{
    /**
     * GET /api/lists
     * Devuelve activas primero, luego historial.
     */
    public function index(Request $request): JsonResponse
    {
        $lists = $request->user()
            ->lists()
            ->with(['items', 'members', 'owner'])
            ->orderByRaw("FIELD(status, 'active', 'paid')")
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json(ListResource::collection($lists));
    }

    /**
     * POST /api/lists
     */
    public function store(StoreListRequest $request): JsonResponse
    {
        $list = ShoppingList::create([
            'owner_id' => $request->user()->id,
            'name'     => $request->name,
            'budget'   => $request->budget,
        ]);

        $list->load(['items', 'members', 'owner']);

        return response()->json(new ListResource($list), 201);
    }

    /**
     * GET /api/lists/{list}
     */
    public function show(ShoppingList $list): JsonResponse
    {
        $this->authorize('view', $list);

        $list->load(['items.addedBy', 'members', 'owner', 'closure.paidBy']);

        return response()->json(new ListResource($list));
    }

    /**
     * PUT /api/lists/{list}
     */
    public function update(UpdateListRequest $request, ShoppingList $list): JsonResponse
    {
        $this->authorize('update', $list);

        $list->update($request->only('name', 'budget'));
        $list->load(['items', 'members', 'owner']);

        return response()->json(new ListResource($list));
    }

    /**
     * DELETE /api/lists/{list}
     */
    public function destroy(ShoppingList $list): JsonResponse
    {
        $this->authorize('delete', $list);

        $list->delete();

        return response()->json(['message' => 'Lista eliminada.']);
    }

    /**
     * POST /api/lists/join
     * Unirse a una lista con código de acceso.
     */
    public function join(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|size:5',
        ]);

        $list = ShoppingList::where('code', strtoupper($request->code))
                            ->where('status', 'active')
                            ->firstOrFail();

        if ($list->hasMember($request->user())) {
            return response()->json(['message' => 'Ya eres miembro de esta lista.'], 422);
        }

        $list->members()->attach($request->user()->id, [
            'role'      => 'editor',
            'joined_at' => now()->timestamp,
        ]);

        $list->load(['items', 'members', 'owner']);

        return response()->json(new ListResource($list));
    }

    /**
     * PUT /api/lists/{list}/close
     * Cierra la lista y registra el pago.
     */
    public function close(CloseListRequest $request, ShoppingList $list): JsonResponse
    {
        $this->authorize('close', $list);

        // Total del carrito calculado en el servidor
        $list->load('items');
        $cartTotal = $list->cart_total;

        $closure = ListClosure::create([
            'list_id'        => $list->id,
            'paid_by'        => $request->paid_by,
            'cart_total'     => $cartTotal,
            'total_real'     => $request->total_real,
            'payment_method' => $request->payment_method,
            'comment'        => $request->comment,
        ]);

        $list->update([
            'status'    => 'paid',
            'closed_at' => now()->timestamp,
        ]);

        $list->load(['items.addedBy', 'members', 'owner', 'closure.paidBy']);

        return response()->json(new ListResource($list));
    }
}
