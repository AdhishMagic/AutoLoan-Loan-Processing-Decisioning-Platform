<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    /**
     * @param  string|null  $value
     * @return string|null
     */
    private function last4(?string $value): ?string
    {
        if (! is_string($value) || $value === '') {
            return null;
        }
        $clean = preg_replace('/[^A-Za-z0-9]/', '', $value) ?? '';
        $len = strlen($clean);
        if ($len === 0) {
            return null;
        }
        return substr($clean, max(0, $len - 4));
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->id,
            'application_number' => $this->application_number,
            'status' => $this->status,
            'requested_amount' => $this->requested_amount,
            'sanctioned_amount' => $this->sanctioned_amount,
            'tenure' => $this->requested_tenure_months ?? $this->tenure_months,
            'created_at' => optional($this->created_at)->toISOString(),
            'pan_last4' => $this->last4($this->pan_number ?? null),
            'aadhaar_last4' => $this->last4($this->aadhaar_number ?? null),
            'bank_account_last4' => $this->last4($this->bank_account_number ?? null),
        ];
    }
}
