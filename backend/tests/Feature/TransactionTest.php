<?php

use App\Enums\CategoryType;
use App\Models\Category;
use App\Models\ChartOfAccount;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createIncomeCoa(): ChartOfAccount
{
    $category = Category::create([
        'name' => 'Test Income',
        'type' => CategoryType::INCOME,
    ]);

    return ChartOfAccount::create([
        'code' => 'TEST-INC',
        'name' => 'Test Income Account',
        'category_id' => $category->id,
    ]);
}

function createExpenseCoa(): ChartOfAccount
{
    $category = Category::create([
        'name' => 'Test Expense',
        'type' => CategoryType::EXPENSE,
    ]);

    return ChartOfAccount::create([
        'code' => 'TEST-EXP',
        'name' => 'Test Expense Account',
        'category_id' => $category->id,
    ]);
}

it('can create an income transaction using credit', function () {
    $coa = createIncomeCoa();

    $response = $this->postJson('/api/v1/transactions', [
        'transaction_date' => '2026-01-01',
        'coa_id' => $coa->id,
        'description' => 'January salary',
        'debit' => 0,
        'credit' => 5000000,
    ]);

    $response
        ->assertSuccessful()
        ->assertJsonPath('data.credit', '5000000.00');

    $this->assertDatabaseHas('transactions', [
        'coa_id' => $coa->id,
        'credit' => 5000000,
        'debit' => 0,
    ]);
});

it('can create an expense transaction using debit', function () {
    $coa = createExpenseCoa();

    $response = $this->postJson('/api/v1/transactions', [
        'transaction_date' => '2026-01-02',
        'coa_id' => $coa->id,
        'description' => 'Transport expense',
        'debit' => 250000,
        'credit' => 0,
    ]);

    $response
        ->assertSuccessful()
        ->assertJsonPath('data.debit', '250000.00');

    $this->assertDatabaseHas('transactions', [
        'coa_id' => $coa->id,
        'debit' => 250000,
        'credit' => 0,
    ]);
});

it('rejects income transaction using debit', function () {
    $coa = createIncomeCoa();

    $response = $this->postJson('/api/v1/transactions', [
        'transaction_date' => '2026-01-01',
        'coa_id' => $coa->id,
        'description' => 'Invalid income',
        'debit' => 5000000,
        'credit' => 0,
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors('credit');

    $this->assertDatabaseCount('transactions', 0);
});

it('rejects expense transaction using credit', function () {
    $coa = createExpenseCoa();

    $response = $this->postJson('/api/v1/transactions', [
        'transaction_date' => '2026-01-02',
        'coa_id' => $coa->id,
        'description' => 'Invalid expense',
        'debit' => 0,
        'credit' => 250000,
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors('debit');

    $this->assertDatabaseCount('transactions', 0);
});

it('rejects transaction when both debit and credit have values', function () {
    $coa = createIncomeCoa();

    $response = $this->postJson('/api/v1/transactions', [
        'transaction_date' => '2026-01-01',
        'coa_id' => $coa->id,
        'description' => 'Invalid transaction',
        'debit' => 100000,
        'credit' => 500000,
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors('amount');

    $this->assertDatabaseCount('transactions', 0);
});

it('rejects transaction when both debit and credit are zero', function () {
    $coa = createIncomeCoa();

    $response = $this->postJson('/api/v1/transactions', [
        'transaction_date' => '2026-01-01',
        'coa_id' => $coa->id,
        'description' => 'Empty transaction',
        'debit' => 0,
        'credit' => 0,
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors('amount');

    $this->assertDatabaseCount('transactions', 0);
});

it('rejects transaction with invalid coa', function () {
    $response = $this->postJson('/api/v1/transactions', [
        'transaction_date' => '2026-01-01',
        'coa_id' => 999999,
        'description' => 'Invalid COA',
        'debit' => 100000,
        'credit' => 0,
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors('coa_id');

    $this->assertDatabaseCount('transactions', 0);
});