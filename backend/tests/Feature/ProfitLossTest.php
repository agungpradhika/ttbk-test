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