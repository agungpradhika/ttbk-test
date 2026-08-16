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

        /**
         * OPTIMASI PERFORMA & MEMORI (TECHNICAL TEST NOTE):
         * 
         * Mengapa menggunakan JOIN & Raw Aggregation (SUM + CASE WHEN) daripada Eloquent Collection?
         * 
         * 1. Efisiensi Memori (RAM):
         *    Jika menggunakan Eloquent `with('chartOfAccount.category')->get()` lalu dijumlahkan di PHP,
         *    ketika data transaksi mencapai 100.000+, PHP harus mengalokasikan RAM untuk membuat ratusan ribu
         *    objek model. Ini akan menyebabkan error "Memory Limit Exceeded". Dengan query ini, database
         *    hanya mengembalikan 1 baris hasil aggregasi, sehingga penggunaan memori di server PHP hampir 0.
         * 
         * 2. Kecepatan Eksekusi (Speed):
         *    Proses penjumlahan dilakukan langsung di level Database Engine (MySQL) menggunakan fungsi native SUM.
         *    Database jauh lebih cepat dan teroptimasi untuk kalkulasi matematika dibandingkan memproses loop di PHP.
         * 
         * 3. Mengurangi Query Terpisah:
         *    Menghindari loading model relasi terpisah (N+1 query) dan menggabungkannya dalam 1 query SQL tunggal.
         */
        // ==========================================
        // PILIHAN A: VERSI SETELAH OPTIMASI (RAW SQL) -> DEFAULT AKTIF
        // ==========================================
        $result = Transaction::query()
            ->join(
                'chart_of_accounts',
                'chart_of_accounts.id',
                '=',
                'transactions.coa_id'
            )
            ->join(
                'categories',
                'categories.id',
                '=',
                'chart_of_accounts.category_id'
            )
            ->whereBetween('transactions.transaction_date', [
                $fromDate,
                $toDate,
            ])
            ->selectRaw(
                'SUM(
                    CASE
                        WHEN categories.type = ? THEN transactions.credit
                        ELSE 0
                    END
                ) AS income',
                [CategoryType::INCOME->value]
            )
            ->selectRaw(
                'SUM(
                    CASE
                        WHEN categories.type = ? THEN transactions.debit
                        ELSE 0
                    END
                ) AS expense',
                [CategoryType::EXPENSE->value]
            )
            ->first();

        $income = $result->income ?? 0;
        $expense = $result->expense ?? 0;

        // ==========================================
        // PILIHAN B: VERSI SEBELUM OPTIMASI (ELOQUENT COLLECTION) -> NON-AKTIF
        // (Uncomment ini dan comment Pilihan A jika ingin mengetes perbedaan kecepatan/memori)
        // ==========================================
        /*
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
        */

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