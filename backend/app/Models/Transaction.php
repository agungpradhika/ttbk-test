<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_date',
        'coa_id',
        'description',
        'debit',
        'credit',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'debit' => 'decimal:2',
            'credit' => 'decimal:2',
        ];
    }

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(
            ChartOfAccount::class,
            'coa_id'
        );
    }
}