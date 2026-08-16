<?php

namespace App\Services;

use App\Enums\CategoryType;
use App\Models\ChartOfAccount;
use App\Models\Transaction;
use Illuminate\Validation\ValidationException;

class TransactionService
{
    public function create(array $data): Transaction
    {
        $coa = ChartOfAccount::with('category')
            ->findOrFail($data['coa_id']);

        $this->validateAmount($data);

        $this->validateCategoryType(
            $coa,
            $data['debit'],
            $data['credit']
        );

        return Transaction::create($data);
    }

    private function validateAmount(array $data): void
    {
        $debit = (float) $data['debit'];
        $credit = (float) $data['credit'];

        if ($debit === 0.0 && $credit === 0.0) {
            throw ValidationException::withMessages([
                'amount' => 'Debit atau kredit harus lebih besar dari nol.',
            ]);
        }

        if ($debit > 0 && $credit > 0) {
            throw ValidationException::withMessages([
                'amount' => 'Hanya salah satu dari debit atau kredit yang boleh bernilai lebih dari nol.',
            ]);
        }
    }

    private function validateCategoryType(
        ChartOfAccount $coa,
        float $debit,
        float $credit
    ): void {
        $type = $coa->category->type;

        if ($type === CategoryType::INCOME && $credit <= 0) {
            throw ValidationException::withMessages([
                'credit' => 'Transaksi pendapatan harus mengisi kolom kredit.',
            ]);
        }

        if ($type === CategoryType::EXPENSE && $debit <= 0) {
            throw ValidationException::withMessages([
                'debit' => 'Transaksi pengeluaran harus mengisi kolom debit.',
            ]);
        }
    }

    public function update(
        Transaction $transaction,
        array $data
    ): Transaction {
        $coa = ChartOfAccount::with('category')
            ->findOrFail($data['coa_id']);

        $this->validateAmount($data);

        $this->validateCategoryType(
            $coa,
            $data['debit'],
            $data['credit']
        );

        $transaction->update($data);

        return $transaction->fresh();
    }
}