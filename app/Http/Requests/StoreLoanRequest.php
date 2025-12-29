<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'loan_amount' => $this->loan_amount !== null ? (int) $this->loan_amount : null,
            'tenure_months' => $this->tenure_months !== null ? (int) $this->tenure_months : null,
            'monthly_income' => $this->monthly_income !== null ? (int) $this->monthly_income : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'loan_product_type' => ['required', Rule::in(['personal', 'home', 'education', 'vehicle'])],
            'loan_amount' => ['required', 'integer', 'min:10000', 'max:5000000'],
            'tenure_months' => ['required', 'integer', 'min:6', 'max:360'],
            'monthly_income' => ['required', 'integer', 'min:1'],
            'employment_type' => ['required', Rule::in(['salaried', 'self_employed'])],
            'aadhaar' => ['required', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
            'pan' => ['required', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'loan_product_type.required' => 'Loan product type is required.',
            'loan_product_type.in' => 'Invalid loan product type.',
            'loan_amount.required' => 'Loan amount is required.',
            'loan_amount.integer' => 'Loan amount must be a number.',
            'loan_amount.min' => 'Loan amount must be at least ₹10,000.',
            'loan_amount.max' => 'Loan amount cannot exceed ₹50,00,000.',
            'tenure_months.required' => 'Tenure is required.',
            'tenure_months.integer' => 'Tenure must be a number.',
            'tenure_months.min' => 'Tenure must be at least 6 months.',
            'tenure_months.max' => 'Tenure cannot exceed 360 months.',
            'monthly_income.required' => 'Monthly income is required.',
            'monthly_income.integer' => 'Monthly income must be a number.',
            'monthly_income.min' => 'Monthly income must be positive.',
            'employment_type.required' => 'Employment type is required.',
            'employment_type.in' => 'Invalid employment type.',
            'aadhaar.required' => 'Aadhaar file is required.',
            'aadhaar.file' => 'Aadhaar must be a file.',
            'aadhaar.mimes' => 'Aadhaar must be a PDF, JPG, or PNG.',
            'aadhaar.max' => 'Aadhaar file may not be greater than 2MB.',
            'pan.required' => 'PAN file is required.',
            'pan.file' => 'PAN must be a file.',
            'pan.mimes' => 'PAN must be a PDF, JPG, or PNG.',
            'pan.max' => 'PAN file may not be greater than 2MB.',
        ];
    }
}
