<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/*
|------------------------------------------------------------------------
| ShopTogether – API Routes
|--------------------------------------------------------------------------
|
| Prefijo automático: /api  (definido en bootstrap/app.php o RouteServiceProvider)
| Autenticación: Laravel Sanctum (token Bearer)
|
*/

// ── Broadcasting auth (Reverb / Echo) ─────────────────────────────────────
Route::middleware('auth:sanctum')->post('/broadcasting/auth', function (\Illuminate\Http\Request $request) {
    return Broadcast::auth($request);
});

// ── Autenticación (pública) ─────────────────────────────────────────────────
Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    // Rutas protegidas
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
    });
});

// ── Rutas protegidas ────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // ── Listas ────────────────────────────────────────────────────────────
    Route::apiResource('lists', ListController::class);

    // Acciones extra sobre listas
    Route::post('lists/join',          [ListController::class, 'join']);   // unirse con código
    Route::put('lists/{list}/close',   [ListController::class, 'close']);  // cerrar y pagar

    // ── Estadísticas ─────────────────────────────────────────────────────
    Route::get('stats', [StatsController::class, 'index']);

    // ── Ítems ─────────────────────────────────────────────────────────────
    // Anidados: GET/POST /api/lists/{list}/items
    Route::apiResource('lists.items', ItemController::class)
         ->only(['index', 'store'])
         ->shallow();

    // PUT/DELETE/PATCH en /api/items/{item}  (shallow)
    Route::put('items/{item}',          [ItemController::class, 'update']);
    Route::delete('items/{item}',       [ItemController::class, 'destroy']);
    Route::patch('items/{item}/toggle', [ItemController::class, 'toggle']);
});
