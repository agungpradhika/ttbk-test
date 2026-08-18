<?php

namespace App\Services;

use App\Enums\CategoryType;
use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;

class ProfitLossService
{
    public function calculate(
        ?string $from = null,
        ?string $to = null
    ): array {
        $fromDate = $from
            ? Carbon::parse($from)->startOfDay()
            : Carbon::create(2026, 1, 1)->startOfDay();

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

        // Hitung rincian per kategori secara dinamis (BE-based breakdown)
        $categoriesWithSums = Category::query()
            ->leftJoin('chart_of_accounts', 'categories.id', '=', 'chart_of_accounts.category_id')
            ->leftJoin('transactions', function ($join) use ($fromDate, $toDate) {
                $join->on('chart_of_accounts.id', '=', 'transactions.coa_id')
                     ->whereBetween('transactions.transaction_date', [$fromDate, $toDate]);
            })
            ->selectRaw('
                categories.id,
                categories.name,
                categories.type,
                SUM(
                    CASE 
                        WHEN categories.type = ? THEN COALESCE(transactions.credit, 0)
                        ELSE COALESCE(transactions.debit, 0)
                    END
                ) as total
            ', [CategoryType::INCOME->value])
            ->groupBy('categories.id', 'categories.name', 'categories.type')
            ->orderBy('categories.name')
            ->get();

        /*
        // ==========================================
        // PILIHAN B: VERSI SEBELUM OPTIMASI (ELOQUENT COLLECTION) -> NON-AKTIF (Hanya untuk demo presentasi perbandingan performa)
        // ==========================================
        // Ambil semua transaksi beserta relasi COA dan Kategori ke memori PHP
        $transactions = Transaction::with(['chartOfAccount.category'])
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->get();

        // Hitung total pendapatan (income) di PHP Collection
        $income = $transactions->filter(function ($t) {
            return $t->chartOfAccount && $t->chartOfAccount->category && $t->chartOfAccount->category->type->value === CategoryType::INCOME->value;
        })->sum('credit');

        // Hitung total pengeluaran (expense) di PHP Collection
        $expense = $transactions->filter(function ($t) {
            return $t->chartOfAccount && $t->chartOfAccount->category && $t->chartOfAccount->category->type->value === CategoryType::EXPENSE->value;
        })->sum('debit');

        // Hitung rincian per kategori secara dinamis menggunakan Eloquent Collection
        $allCategories = Category::all();
        $categoriesWithSums = $allCategories->map(function ($category) use ($transactions) {
            $total = $transactions->filter(function ($t) use ($category) {
                return $t->chartOfAccount && $t->chartOfAccount->category_id && $t->chartOfAccount->category_id === $category->id;
            })->sum($category->type->value === CategoryType::INCOME->value ? 'credit' : 'debit');

            return (object) [
                'id' => $category->id,
                'name' => $category->name,
                'type' => $category->type,
                'total' => $total,
            ];
        });
        */

        return [
            'period' => [
                'from' => $fromDate->format('Y-m-d'),
                'to' => $toDate->format('Y-m-d'),
            ],
            'income' => $income,
            'expense' => $expense,
            'net_profit' => $income - $expense,
            'categories' => $categoriesWithSums->map(fn($cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
                'type' => $cat->type instanceof CategoryType ? $cat->type->value : $cat->type,
                'total' => (float)($cat->total ?? 0),
            ])->toArray(),
        ];
    }
}