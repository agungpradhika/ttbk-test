<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\ChartOfAccount;
use Carbon\Carbon;

class GenerateHugeData extends Command
{
    protected $signature = 'app:generate-huge-data {count=150000}';
    protected $description = 'Generate massive transaction testing data from Jan to Aug 2026';

    public function handle()
    {
        $this->info("Initializing database seeds...");

        // Ambil COA yang terdaftar
        $coas = ChartOfAccount::with('category')->get();

        if ($coas->isEmpty()) {
            $this->error("Please run the seeders first (php artisan db:seed) to populate categories and COA!");
            return 1;
        }

        $count = (int) $this->argument('count');
        $this->info("Generating {$count} transactions...");

        $startDate = Carbon::create(2026, 1, 1)->startOfDay();
        $endDate = Carbon::create(2026, 8, 31)->endOfDay();
        $totalDays = $startDate->diffInDays($endDate);

        $chunks = [];
        $chunkSize = 5000;
        $now = Carbon::now('Asia/Jakarta');

        $incomeCoas = $coas->filter(fn($c) => $c->category->type->value === 'income')->values();
        $expenseCoas = $coas->filter(fn($c) => $c->category->type->value === 'expense')->values();

        for ($i = 0; $i < $count; $i++) {
            // Tentukan apakah transaksi ini Income atau Expense (misal 30% income, 70% expense)
            $isIncome = rand(1, 100) <= 30;
            
            if ($isIncome && $incomeCoas->isNotEmpty()) {
                $coa = $incomeCoas->random();
                $debit = 0;
                $credit = rand(100000, 5000000); // 100k s/d 5M
            } else {
                $coa = $expenseCoas->isNotEmpty() ? $expenseCoas->random() : $coas->random();
                $debit = rand(5000, 500000); // 5k s/d 500k
                $credit = 0;
            }

            // Tanggal acak antara Januari s/d Agustus 2026
            $randomDate = (clone $startDate)->addDays(rand(0, $totalDays))->addHours(rand(0, 23))->addMinutes(rand(0, 59));

            $chunks[] = [
                'coa_id' => $coa->id,
                'transaction_date' => $randomDate->format('Y-m-d'),
                'description' => "Testing transaction #{$i} for " . strtolower($coa->name),
                'debit' => $debit,
                'credit' => $credit,
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ];

            if (count($chunks) >= $chunkSize) {
                DB::table('transactions')->insert($chunks);
                $chunks = [];
                $this->output->write('.');
            }
        }

        if (count($chunks) > 0) {
            DB::table('transactions')->insert($chunks);
        }

        $this->newLine();
        $this->info("Successfully generated {$count} transactions in the database!");
        return 0;
    }
}
