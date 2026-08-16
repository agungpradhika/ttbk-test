<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionService;
use App\Http\Requests\UpdateTransactionRequest;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService
    ) {}

    public function index()
    {
        return TransactionResource::collection(
            Transaction::query()
                ->with('chartOfAccount.category')
                ->latest('transaction_date')
                ->paginate(20)
        );
    }

    public function store(
        StoreTransactionRequest $request
    ): TransactionResource {
        $transaction = $this->transactionService
            ->create($request->validated());

        return new TransactionResource(
            $transaction->load('chartOfAccount.category')
        );
    }

    public function show(Transaction $transaction): TransactionResource
    {
        return new TransactionResource(
            $transaction->load('chartOfAccount.category')
        );
    }

    public function update(
        UpdateTransactionRequest $request,
        Transaction $transaction
    ): TransactionResource {
        $transaction = $this->transactionService->update(
            $transaction,
            $request->validated()
        );

        return new TransactionResource(
            $transaction->load('chartOfAccount.category')
        );
    }

    public function destroy(Transaction $transaction): JsonResponse
    {
        $transaction->delete();

        return response()->json([
            'message' => 'Transaction deleted successfully.',
        ]);
    }
}