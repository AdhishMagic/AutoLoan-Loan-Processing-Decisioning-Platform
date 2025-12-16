<?php

namespace App\Http\Requests;

use App\Models\LoanApplication;
use Illuminate\Foundation\Http\FormRequest;

class StoreLoanApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', LoanApplication::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'loan_type' => ['required', 'string', 'max:50'],
            'requested_amount' => ['required', 'numeric', 'min:0'],
            'tenure_months' => ['required', 'integer', 'min:1'],
        ];
    }
}
