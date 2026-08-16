<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Enums\CategoryType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $year = $request->integer('year', 2026);

        // Agregasi bulanan berkinerja tinggi untuk data massal
        $chartData = Transaction::query()
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'transactions.coa_id')
            ->join('categories', 'categories.id', '=', 'chart_of_accounts.category_id')
            ->whereYear('transactions.transaction_date', $year)
            ->selectRaw('
                MONTH(transactions.transaction_date) as month,
                SUM(CASE WHEN categories.type = ? THEN transactions.credit ELSE 0 END) as income,
                SUM(CASE WHEN categories.type = ? THEN transactions.debit ELSE 0 END) as expense
            ', [CategoryType::INCOME->value, CategoryType::EXPENSE->value])
            ->groupByRaw('MONTH(transactions.transaction_date)')
            ->orderBy('month')
            ->get();

        // 12 bulan standar
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];

        $labels = [];
        $incomeData = [];
        $expenseData = [];

        foreach ($months as $num => $name) {
            $labels[] = $name;
            $found = $chartData->firstWhere('month', $num);
            $incomeData[] = (float)($found ? $found->income : 0);
            $expenseData[] = (float)($found ? $found->expense : 0);
        }

        return response()->json([
            'labels' => $labels,
            'income' => $incomeData,
            'expense' => $expenseData,
        ]);
    }
}
