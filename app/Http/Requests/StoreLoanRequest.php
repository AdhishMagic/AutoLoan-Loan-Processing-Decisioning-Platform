<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\LoanApplication;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', LoanApplication::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'loan_product_type' => ['required', 'string', Rule::in(['HOME_LOAN', 'AUTO_LOAN', 'PERSONAL_LOAN'])],
            'requested_amount' => ['required', 'numeric', 'min:10000'],
            'requested_tenure_months' => ['required', 'integer', 'min:6', 'max:360'],
            'income' => ['required', 'numeric', 'min:1000'],
            'employment_type' => ['nullable', 'string', Rule::in(['SALARIED', 'SELF_EMPLOYED', 'GOVERNMENT'])],
        ];
    }
}
