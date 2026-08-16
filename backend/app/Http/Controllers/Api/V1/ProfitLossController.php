<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ProfitLossService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfitLossController extends Controller
{
    public function __construct(
        private readonly ProfitLossService $profitLossService
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        return response()->json([
            'data' => $this->profitLossService->calculate(
                $validated['from'] ?? null,
                $validated['to'] ?? null
            ),
        ]);
    }
}