<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $paidLists = $user->lists()
            ->with(['closure'])
            ->where('status', 'paid')
            ->orderBy('closed_at', 'asc')
            ->get()
            ->filter(fn($l) => $l->closure);

        if ($paidLists->isEmpty()) {
            return response()->json([
                'total_spent'   => 0,
                'total_lists'   => 0,
                'average_spent' => 0,
                'max_spent'     => 0,
                'min_spent'     => 0,
                'evolution'     => [],
                'comparisons'   => [],
                'monthly'       => [],
            ]);
        }

        $amounts = $paidLists->map(fn($l) => $l->closure->total_real);

        $totalSpent = $amounts->sum();
        $totalLists = $amounts->count();
        $avgSpent   = $totalLists > 0 ? round($totalSpent / $totalLists) : 0;
        $maxSpent   = $amounts->max() ?? 0;
        $minSpent   = $amounts->min() ?? 0;

        // Evolución por lista
        $evolution = $paidLists->values()->map(fn($l) => [
            'label'  => $l->name,
            'amount' => $l->closure->total_real,
            'date'   => $l->closed_at,
        ]);

        // Comparativa presupuesto vs real
        $comparisons = $paidLists->values()->map(fn($l) => [
            'id'         => $l->id,
            'name'       => $l->name,
            'budget'     => $l->budget,
            'cart_total' => $l->closure->cart_total,
            'total_real' => $l->closure->total_real,
            'diff'       => $l->budget ? $l->closure->total_real - $l->budget : null,
            'date'       => $l->closed_at,
            'method'     => $l->closure->payment_method,
        ]);

        // Agrupado por mes
        $monthly = $paidLists
            ->groupBy(fn($l) => date('Y-m', $l->closed_at))
            ->map(function ($group, $key) {
                $ts     = strtotime($key . '-01');
                $total  = $group->sum(fn($l) => $l->closure->total_real);
                $count  = $group->count();
                return [
                    'key'     => $key,                         // "2026-01"
                    'label'   => ucfirst(\Carbon\Carbon::createFromTimestamp($ts)->locale('es')->isoFormat('MMMM YYYY')),
                    'ts'      => $ts,
                    'total'   => $total,
                    'count'   => $count,
                    'average' => $count > 0 ? round($total / $count) : 0,
                    'lists'   => $group->values()->map(fn($l) => [
                        'id'         => $l->id,
                        'name'       => $l->name,
                        'budget'     => $l->budget,
                        'total_real' => $l->closure->total_real,
                        'diff'       => $l->budget ? $l->closure->total_real - $l->budget : null,
                        'method'     => $l->closure->payment_method,
                        'date'       => $l->closed_at,
                    ])->values(),
                ];
            })
            ->sortBy('ts')
            ->values();

        return response()->json([
            'total_spent'   => $totalSpent,
            'total_lists'   => $totalLists,
            'average_spent' => $avgSpent,
            'max_spent'     => $maxSpent,
            'min_spent'     => $minSpent,
            'evolution'     => $evolution,
            'comparisons'   => $comparisons,
            'monthly'       => $monthly,
        ]);
    }
}
