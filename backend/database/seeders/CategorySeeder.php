<?php

namespace Database\Seeders;

use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create([
            'name' => 'Salary',
            'type' => CategoryType::INCOME,
        ]);

        Category::create([
            'name' => 'Other Income',
            'type' => CategoryType::INCOME,
        ]);

        Category::create([
            'name' => 'Family Expense',
            'type' => CategoryType::EXPENSE,
        ]);

        Category::create([
            'name' => 'Transport Expense',
            'type' => CategoryType::EXPENSE,
        ]);

        Category::create([
            'name' => 'Meal Expense',
            'type' => CategoryType::EXPENSE,
        ]);
    }
}