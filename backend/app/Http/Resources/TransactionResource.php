<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_date' => $this->transaction_date->format('Y-m-d'),
            'description' => $this->description,
            'debit' => $this->debit,
            'credit' => $this->credit,

            'coa' => [
                'id' => $this->chartOfAccount->id,
                'code' => $this->chartOfAccount->code,
                'name' => $this->chartOfAccount->name,
            ],

            'category' => [
                'id' => $this->chartOfAccount->category->id,
                'name' => $this->chartOfAccount->category->name,
                'type' => $this->chartOfAccount->category->type->value,
            ],
        ];
    }
}