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

    public function index(\Illuminate\Http\Request $request)
    {
        $query = Transaction::query()->with('chartOfAccount.category');

        if ($request->filled('from')) {
            $query->whereDate('transaction_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('transaction_date', '<=', $request->to);
        }

        if ($request->filled('coa_id')) {
            $query->where('coa_id', $request->coa_id);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->integer('per_page', 25);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 25;
        }

        return TransactionResource::collection(
            $query->latest('transaction_date')->paginate($perPage)
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