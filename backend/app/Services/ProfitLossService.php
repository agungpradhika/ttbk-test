<?php

namespace App\Services;

use App\Enums\CategoryType;
use App\Models\Transaction;
use Carbon\Carbon;

class ProfitLossService
{
    public function calculate(
        ?string $from = null,
        ?string $to = null
    ): array {
        $fromDate = $from
            ? Carbon::parse($from)->startOfDay()
            : Carbon::create(1970, 1, 1)->startOfDay();

        $toDate = $to
            ? Carbon::parse($to)->endOfDay()
            : now()->endOfDay();

        $transactions = Transaction::query()
            ->with('chartOfAccount.category')
            ->whereBetween('transaction_date', [
                $fromDate,
                $toDate,
            ])
            ->get();

        $income = $transactions
            ->filter(
                fn ($transaction) =>
                    $transaction->chartOfAccount->category->type
                    === CategoryType::INCOME
            )
            ->sum('credit');

        $expense = $transactions
            ->filter(
                fn ($transaction) =>
                    $transaction->chartOfAccount->category->type
                    === CategoryType::EXPENSE
            )
            ->sum('debit');

        return [
            'period' => [
                'from' => $fromDate->format('Y-m-d'),
                'to' => $toDate->format('Y-m-d'),
            ],
            'income' => $income,
            'expense' => $expense,
            'net_profit' => $income - $expense,
        ];
    }
}