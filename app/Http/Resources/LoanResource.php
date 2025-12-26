<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
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
        ];
    }
}
