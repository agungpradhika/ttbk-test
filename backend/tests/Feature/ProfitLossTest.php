<?php

use App\Enums\CategoryType;
use App\Models\Category;
use App\Models\ChartOfAccount;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createIncomeAccount(): ChartOfAccount
{
    $category = Category::create([
        'name' => 'Salary',
        'type' => CategoryType::INCOME,
    ]);

    return ChartOfAccount::create([
        'code' => '4001',
        'name' => 'Salary Income',
        'category_id' => $category->id,
    ]);
}

function createExpenseAccount(): ChartOfAccount
{
    $category = Category::create([
        'name' => 'Transport',
        'type' => CategoryType::EXPENSE,
    ]);

    return ChartOfAccount::create([
        'code' => '5001',
        'name' => 'Transport Expense',
        'category_id' => $category->id,
    ]);
}

it('calculates profit and loss correctly', function () {
    $incomeCoa = createIncomeAccount();
    $expenseCoa = createExpenseAccount();

    // 1. Catat Pendapatan
    Transaction::create([
        'transaction_date' => '2026-01-01',
        'coa_id' => $incomeCoa->id,
        'description' => 'Gaji Bulanan',
        'debit' => 0,
        'credit' => 5000000,
    ]);

    // 2. Catat Pengeluaran
    Transaction::create([
        'transaction_date' => '2026-01-02',
        'coa_id' => $expenseCoa->id,
        'description' => 'Beli Bensin',
        'debit' => 1500000,
        'credit' => 0,
    ]);

    // Hit Endpoint Laporan Laba Rugi
    $response = $this->getJson('/api/v1/profit-loss');

    $response
        ->assertSuccessful()
        ->assertJsonPath('data.income', 5000000)
        ->assertJsonPath('data.expense', 1500000)
        ->assertJsonPath('data.net_profit', 3500000);
});

it('calculates profit and loss within date range', function () {
    $incomeCoa = createIncomeAccount();

    // Transaksi didalam range tanggal (2026-01-05)
    Transaction::create([
        'transaction_date' => '2026-01-05',
        'coa_id' => $incomeCoa->id,
        'description' => 'Proyek A',
        'debit' => 0,
        'credit' => 3000000,
    ]);

    // Transaksi diluar range tanggal (2026-02-01)
    Transaction::create([
        'transaction_date' => '2026-02-01',
        'coa_id' => $incomeCoa->id,
        'description' => 'Proyek B',
        'debit' => 0,
        'credit' => 2000000,
    ]);

    // Filter dari tanggal 2026-01-01 s/d 2026-01-31
    $response = $this->getJson('/api/v1/profit-loss?from=2026-01-01&to=2026-01-31');

    $response
        ->assertSuccessful()
        ->assertJsonPath('data.income', 3000000) // Proyek B (2jt) di luar range tidak ikut dihitung
        ->assertJsonPath('data.net_profit', 3000000);
});