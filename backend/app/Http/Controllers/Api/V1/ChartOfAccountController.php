<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChartOfAccountRequest;
use App\Http\Requests\UpdateChartOfAccountRequest;
use App\Http\Resources\ChartOfAccountResource;
use App\Models\ChartOfAccount;
use Illuminate\Http\JsonResponse;

class ChartOfAccountController extends Controller
{
    public function index()
    {
        return ChartOfAccountResource::collection(
            ChartOfAccount::query()
                ->with('category')
                ->orderBy('code')
                ->paginate(15)
        );
    }

    public function store(
        StoreChartOfAccountRequest $request
    ): ChartOfAccountResource {
        $coa = ChartOfAccount::create($request->validated());

        return new ChartOfAccountResource(
            $coa->load('category')
        );
    }

    public function show(
        ChartOfAccount $chartOfAccount
    ): ChartOfAccountResource {
        return new ChartOfAccountResource(
            $chartOfAccount->load('category')
        );
    }

    public function update(
        UpdateChartOfAccountRequest $request,
        ChartOfAccount $chartOfAccount
    ): ChartOfAccountResource {
        $chartOfAccount->update($request->validated());

        return new ChartOfAccountResource(
            $chartOfAccount->fresh()->load('category')
        );
    }

    public function destroy(
        ChartOfAccount $chartOfAccount
    ): JsonResponse {
        if ($chartOfAccount->transactions()->exists()) {
            return response()->json([
                'message' => 'COA cannot be deleted because it is being used.',
            ], 422);
        }

        $chartOfAccount->delete();

        return response()->json([
            'message' => 'COA deleted successfully.',
        ]);
    }
}