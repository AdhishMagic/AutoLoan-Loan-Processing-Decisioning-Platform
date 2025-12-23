<?php

namespace App\Http\Requests;

use App\Models\LoanApplication;
use App\Models\LoanDocument;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLoanDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var LoanApplication|null $loan */
        $loan = $this->route('loan');

        if (! $loan) {
            return false;
        }

        return $this->user()?->can('create', [LoanDocument::class, $loan]) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'document_type' => ['required', 'string', Rule::in(LoanDocument::supportedTypes())],
            'file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ],
        ];
    }
}
