<?php

namespace App\Http\Requests;

use App\Models\LoanApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLoanApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', LoanApplication::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $step = (int) $this->route('step');

        // Normalize inputs for Step 2 (Applicant details)
        if ($step === 2) {
            $aadhaar = preg_replace('/\D+/', '', (string) $this->input('aadhaar_number'));
            $mobile = preg_replace('/\D+/', '', (string) $this->input('mobile'));
            $pan = strtoupper((string) $this->input('pan_number'));
            $gender = strtoupper((string) $this->input('gender'));
            $marital = strtoupper((string) $this->input('marital_status'));

            $this->merge([
                'aadhaar_number' => $aadhaar,
                'mobile' => $mobile,
                'pan_number' => $pan,
                'gender' => $gender,
                'marital_status' => $marital,
            ]);
        }

        // Normalize inputs for Step 3 (Employment & Income)
        if ($step === 3) {
            $employmentType = strtoupper((string) $this->input('employment_type'));
            $companyName = trim((string) $this->input('company_name'));
            $expYears = (int) ($this->input('total_experience_years') ?? 0);

            $this->merge([
                'employment_type' => $employmentType,
                'company_name' => $companyName,
                'total_experience_years' => $expYears,
            ]);
        }

        // Normalize inputs for Step 5 (Property)
        if ($step === 5) {
            $typeRaw = (string) $this->input('property_type');
            $statusRaw = (string) $this->input('construction_status');
            $ownershipRaw = (string) $this->input('ownership_type');

            $normalize = function ($v) {
                $v = strtoupper(trim($v));
                $v = str_replace([' / ', '/', ' '], '_', $v);
                return $v;
            };

            $propertyType = $normalize($typeRaw);
            // Map common friendly values to enum
            if ($propertyType === 'PLOT_LAND') {
                $propertyType = 'RESIDENTIAL_PLOT';
            }

            $constructionStatus = $normalize($statusRaw);
            $ownershipType = $normalize($ownershipRaw);

            $this->merge([
                'property_type' => $propertyType,
                'construction_status' => $constructionStatus,
                'ownership_type' => $ownershipType,
            ]);
        }

        // Normalize inputs for Step 6 (References)
        if ($step === 6) {
            $normalize = function ($v) {
                $v = strtoupper(trim((string) $v));
                $v = str_replace([' / ', '/', ' '], '_', $v);
                return $v;
            };
            $this->merge([
                'ref_1_relation' => $normalize($this->input('ref_1_relation')),
            ]);
        }
    }

    public function rules(): array
    {
        $step = (int) $this->route('step');

        $rules = [];

        // Common/Default rules or Step 1
        if ($step === 1 || !$step) {
            $rules = [
                'loan_product_type' => ['required', 'string', 'max:50'],
                'requested_amount' => ['required', 'numeric', 'min:0'],
                'requested_tenure_months' => ['required', 'integer', 'min:1'],
            ];
        }

        // Step 2: Applicants
        if ($step === 2) {
            $loan = $this->route('loan');
            $loanId = is_object($loan) ? ($loan->id ?? $loan->getKey()) : $loan;
            $primaryApplicantId = null;
            if (is_object($loan) && method_exists($loan, 'primaryApplicant')) {
                $primaryApplicantId = optional($loan->primaryApplicant()->first())->id;
            }
            $rules = [
                'first_name' => ['required', 'string'],
                'last_name' => ['required', 'string'],
                'date_of_birth' => ['required', 'date'],
                'pan_number' => [
                    'required', 'string', 'size:10', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/',
                    Rule::unique('applicants', 'pan_number')
                        ->where(fn($q) => $q->where('loan_application_id', $loanId))
                        ->ignore($primaryApplicantId),
                ],
                'aadhaar_number' => [
                    'required', 'digits:12',
                    Rule::unique('applicants', 'aadhaar_number')
                        ->where(fn($q) => $q->where('loan_application_id', $loanId))
                        ->ignore($primaryApplicantId),
                ],
                'mobile' => ['required', 'digits:10'],
                'email' => ['required', 'email'],
                'gender' => ['required', 'in:MALE,FEMALE,OTHER'],
                'marital_status' => ['required', 'in:SINGLE,MARRIED,DIVORCED,WIDOWED'],
                // Add more as needed
            ];
        }

        // Step 3: Employment
        if ($step === 3) {
            $rules = [
                'employment_type' => ['required', 'in:SALARIED,SELF_EMPLOYED_PROFESSIONAL,SELF_EMPLOYED_BUSINESS,RETIRED,UNEMPLOYED,STUDENT,HOMEMAKER'],
                'company_name' => ['nullable', 'string', 'max:200'],
                'total_experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
                'gross_income' => ['required', 'numeric', 'min:0'],
                'net_income' => ['nullable', 'numeric', 'min:0'],
            ];
        }

        // Step 5: Property
        if ($step === 5) {
             $rules = [
                 'property_type' => ['required', 'in:RESIDENTIAL_FLAT,RESIDENTIAL_VILLA,RESIDENTIAL_BUNGALOW,RESIDENTIAL_PLOT,COMMERCIAL_OFFICE,COMMERCIAL_SHOP,COMMERCIAL_PLOT,INDUSTRIAL,AGRICULTURAL,MIXED_USE,OTHER'],
                 'property_sub_type' => ['nullable', 'in:1BHK,2BHK,3BHK,4BHK,STUDIO,PENTHOUSE,NOT_APPLICABLE'],
                 'construction_status' => ['required', 'in:READY_TO_MOVE,UNDER_CONSTRUCTION,NEW_BOOKING,RESALE'],
                 'ownership_type' => ['required', 'in:SELF,JOINT,FAMILY,ANCESTRAL,COMPANY,TRUST,OTHER'],
                 'market_value' => ['required', 'numeric', 'min:0'],
                 'property_address' => ['nullable', 'string', 'max:255'],
                 'property_city' => ['nullable', 'string', 'max:100'],
                 'property_state' => ['nullable', 'string', 'max:100'],
                 'property_pincode' => ['nullable', 'string', 'max:10'],
             ];
        }

        // Step 6: References
        if ($step === 6) {
            $rules = [
                'ref_1_name' => ['nullable', 'string', 'max:200'],
                'ref_1_relation' => ['nullable', 'in:FATHER,MOTHER,BROTHER,SISTER,SPOUSE,FRIEND,COLLEAGUE,MANAGER,NEIGHBOR,BUSINESS_ASSOCIATE,OTHER'],
                'ref_1_mobile' => ['nullable', 'digits:10'],
                'ref_1_address' => ['nullable', 'string', 'max:500'],
            ];
        }

        return $rules;
    }
}
