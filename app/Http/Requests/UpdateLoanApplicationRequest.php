<?php

namespace App\Http\Requests;

use App\Models\LoanApplication;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLoanApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var LoanApplication|null $loan */
        $loan = $this->route('loan');
        return $loan ? $this->user()?->can('update', $loan) ?? false : false;
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
