<?php

namespace Database\Seeders;

use App\Enums\CategoryType;
use App\Models\Category;
use App\Models\ChartOfAccount;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class AccountingTestSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        $salary = Category::updateOrCreate(
            ['name' => 'Salary'],
            ['type' => CategoryType::INCOME]
        );

        $otherIncome = Category::updateOrCreate(
            ['name' => 'Other Income'],
            ['type' => CategoryType::INCOME]
        );

        $familyExpense = Category::updateOrCreate(
            ['name' => 'Family Expense'],
            ['type' => CategoryType::EXPENSE]
        );

        $transportExpense = Category::updateOrCreate(
            ['name' => 'Transport Expense'],
            ['type' => CategoryType::EXPENSE]
        );

        $mealExpense = Category::updateOrCreate(
            ['name' => 'Meal Expense'],
            ['type' => CategoryType::EXPENSE]
        );

        /*
        |--------------------------------------------------------------------------
        | Chart of Accounts
        |--------------------------------------------------------------------------
        */

        $salaryCoa = ChartOfAccount::updateOrCreate(
            ['code' => '4001'],
            [
                'name' => 'Salary Income',
                'category_id' => $salary->id,
            ]
        );

        $otherIncomeCoa = ChartOfAccount::updateOrCreate(
            ['code' => '4002'],
            [
                'name' => 'Other Income',
                'category_id' => $otherIncome->id,
            ]
        );

        $familyExpenseCoa = ChartOfAccount::updateOrCreate(
            ['code' => '5001'],
            [
                'name' => 'Family Expense',
                'category_id' => $familyExpense->id,
            ]
        );

        $transportExpenseCoa = ChartOfAccount::updateOrCreate(
            ['code' => '5002'],
            [
                'name' => 'Transport Expense',
                'category_id' => $transportExpense->id,
            ]
        );

        $mealExpenseCoa = ChartOfAccount::updateOrCreate(
            ['code' => '5003'],
            [
                'name' => 'Meal Expense',
                'category_id' => $mealExpense->id,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Transactions - January 2026
        |--------------------------------------------------------------------------
        */

        Transaction::updateOrCreate(
            [
                'transaction_date' => '2026-01-01',
                'coa_id' => $salaryCoa->id,
                'description' => 'January Salary',
            ],
            [
                'debit' => 0,
                'credit' => 5_000_000,
            ]
        );

        Transaction::updateOrCreate(
            [
                'transaction_date' => '2026-01-05',
                'coa_id' => $familyExpenseCoa->id,
                'description' => 'Family Expense',
            ],
            [
                'debit' => 500_000,
                'credit' => 0,
            ]
        );

        Transaction::updateOrCreate(
            [
                'transaction_date' => '2026-01-10',
                'coa_id' => $transportExpenseCoa->id,
                'description' => 'Transport Expense',
            ],
            [
                'debit' => 750_000,
                'credit' => 0,
            ]
        );

        Transaction::updateOrCreate(
            [
                'transaction_date' => '2026-01-15',
                'coa_id' => $otherIncomeCoa->id,
                'description' => 'Freelance Income',
            ],
            [
                'debit' => 0,
                'credit' => 2_000_000,
            ]
        );

        Transaction::updateOrCreate(
            [
                'transaction_date' => '2026-01-20',
                'coa_id' => $mealExpenseCoa->id,
                'description' => 'Meal Expense',
            ],
            [
                'debit' => 250_000,
                'credit' => 0,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Transactions - February 2026
        |--------------------------------------------------------------------------
        */

        Transaction::updateOrCreate(
            [
                'transaction_date' => '2026-02-01',
                'coa_id' => $salaryCoa->id,
                'description' => 'February Salary',
            ],
            [
                'debit' => 0,
                'credit' => 5_000_000,
            ]
        );

        Transaction::updateOrCreate(
            [
                'transaction_date' => '2026-02-05',
                'coa_id' => $transportExpenseCoa->id,
                'description' => 'February Transport',
            ],
            [
                'debit' => 600_000,
                'credit' => 0,
            ]
        );
    }
}