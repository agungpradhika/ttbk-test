<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChartOfAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // mendapatkann ID chart_of_account dari parameter route
        $chartOfAccountId = $this->route('chart_of_account');
        
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                // unik kecuali unntuk record yg sedang diupdate
                Rule::unique('chart_of_accounts', 'code')->ignore($chartOfAccountId)
            ],
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'category_id' => [
                'required',
                'exists:categories,id'
            ],
        ];
    }
}
