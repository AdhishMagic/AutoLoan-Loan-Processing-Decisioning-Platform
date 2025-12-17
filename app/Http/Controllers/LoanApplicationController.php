<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanApplicationRequest;
use App\Http\Requests\UpdateLoanApplicationRequest;
use App\Models\LoanApplication;
use App\Services\LoanApplicationService;
use App\Jobs\ProcessLoanApplication;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LoanApplicationController extends Controller
{
    public function index(): View
    {
        $loans = LoanApplication::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('loans.index', compact('loans'));
    }

    public function create(): RedirectResponse
    {
        // Auto-create a draft application
        $loan = LoanApplication::create([
            'user_id' => auth()->id(),
            'status' => 'DRAFT',
            'application_number' => 'LAP-' . strtoupper(uniqid()), // Simple ID generation
            'stage_order' => 1,
            'application_date' => now(),
        ]);

        return redirect()->route('loans.step.show', ['loan' => $loan->id, 'step' => 1]);
    }

    // Wizard Step Handler
    public function showStep(LoanApplication $loan, int $step): View|RedirectResponse
    {
        Gate::authorize('view', $loan);
        
        // Basic security: Prevent skipping ahead too far
        // Assuming 'stage_order' tracks the furthest completed step (0-7)
        // If user tries to access Step 5 but is on Step 2, redirect to Step 3.
        $maxStep = ($loan->stage_order ?? 0) + 1;
        
        // Allow reviewing previous steps anytime.
        if ($step > $maxStep && $loan->status === 'DRAFT') {
            return redirect()->route('loans.step.show', ['loan' => $loan->id, 'step' => $maxStep]);
        }

        return view('loans.step_' . $step, compact('loan', 'step'));
    }

    public function storeStep(StoreLoanApplicationRequest $request, LoanApplication $loan, int $step): RedirectResponse
    {
        Gate::authorize('update', $loan);
        
        $data = $request->validated();
        
        DB::transaction(function () use ($loan, $step, $request) {
            
            // Handle specific step logic
            switch ($step) {
                case 1: // Loan Overview
                    $loan->update($request->only([
                        'loan_product_type', 'loan_purpose', 
                        'requested_amount', 'requested_tenure_months'
                    ]));
                    break;

                case 2: // Applicants (Primary)
                    try {
                        // Create or update by role within this loan
                        $loan->applicants()->updateOrCreate(
                            ['applicant_role' => 'PRIMARY'],
                            $request->only([
                                'first_name', 'last_name', 'date_of_birth', 'gender', 'marital_status',
                                'pan_number', 'aadhaar_number', 'mobile', 'email'
                            ]) + ['applicant_role' => 'PRIMARY']
                        );
                    } catch (QueryException $e) {
                        // Handle unique constraint violations gracefully (Postgres 23505)
                        if ((int) ($e->getCode()) === 23505 || str_contains(strtolower($e->getMessage()), 'unique')) {
                            $message = 'Duplicate value detected.';
                            $lower = strtolower($e->getMessage());
                            if (str_contains($lower, 'aadhaar')) {
                                $message = 'This Aadhaar number is already used in another application.';
                            } elseif (str_contains($lower, 'pan')) {
                                $message = 'This PAN number is already used in another application.';
                            }
                            return back()->withErrors(['aadhaar_number' => $message])->withInput();
                        }
                        throw $e;
                    }
                    
                    // Update Current Address
                    // Assuming simplified address handling for now
                    if ($request->filled('current_address_line_1')) {
                        $applicant = $loan->primaryApplicant()->first();
                        $applicant?->addresses()->updateOrCreate(
                            ['address_type' => 'CURRENT'],
                            [
                                'address_line_1' => $request->current_address_line_1,
                                'city' => $request->current_city,
                                'state' => $request->current_state,
                                'pincode' => $request->current_pincode,
                                'country' => 'INDIA',
                            ]
                        );
                    }
                    break;

                case 3: // Employment & Income
                    $applicant = $loan->primaryApplicant->first();
                    if ($applicant) {
                        // Update Employment
                        $applicant->employmentDetails()->updateOrCreate(
                            ['employment_status' => 'CURRENT'], // Simple identifier
                            $request->only(['employment_type', 'total_experience_years', 'company_name'])
                        );
                        
                        // Update Income
                        $employmentType = strtoupper((string) $request->employment_type);
                        $incomeType = match ($employmentType) {
                            'SALARIED' => 'SALARY',
                            'SELF_EMPLOYED_PROFESSIONAL' => 'PROFESSIONAL_INCOME',
                            'SELF_EMPLOYED_BUSINESS' => 'BUSINESS_INCOME',
                            'RETIRED' => 'PENSION',
                            default => 'OTHER',
                        };

                        $applicant->incomeDetails()->updateOrCreate(
                            ['income_type' => $incomeType], 
                            [
                                'income_frequency' => 'MONTHLY',
                                'gross_income_amount' => $request->gross_income,
                                'net_income_amount' => $request->net_income ?? 0,
                            ]
                        );
                    }
                    break;
                    
                case 4: // Financials (Bank)
                     $applicant = $loan->primaryApplicant->first();
                     if ($applicant && $request->filled('account_number')) {
                         $applicant->bankAccounts()->updateOrCreate(
                             ['account_number' => $request->account_number],
                             $request->only(['bank_name', 'account_type', 'ifsc_code'])
                         );
                     }
                     // Repeater logic for existing loans would be handled here (skipping for brevity but acknowledged)
                     break;

                case 5: // Property
                    // Update or Create Property
                    $property = $loan->properties()->firstOrNew([]);
                    $property->fill($request->only(['property_type', 'property_sub_type', 'construction_status', 'ownership_type', 'market_value']));
                    $property->save();
                    
                    if ($request->filled('property_address')) {
                        $property->addresses()->updateOrCreate(
                            ['address_type' => 'PROPERTY'],
                            [
                                'address_line_1' => $request->property_address,
                                'city' => $request->property_city,
                                'state' => $request->property_state,
                                'pincode' => $request->property_pincode,
                            ]
                        );
                    }
                    break;

                case 6: // References
                     // Clear old and add new (Naive approach) or Update
                     if ($request->filled('ref_1_name')) {
                         $relation = strtoupper(str_replace([' / ', '/', ' '], '_', (string) $request->ref_1_relation));
                         $loan->references()->updateOrCreate(
                             // Use mobile as a stable dedupe key per loan
                             ['mobile' => $request->ref_1_mobile ?? ''],
                             [
                                 'reference_type' => 'PERSONAL',
                                 'full_name' => $request->ref_1_name,
                                 'relationship' => $relation ?: null,
                                 'mobile' => $request->ref_1_mobile,
                                 'address' => $request->ref_1_address,
                             ]
                         );
                     }
                     break;

                case 7: // Declarations
                     // Save Declarations
                     // PEP declaration (mapped to GENERAL_DECLARATION type)
                     $loan->declarations()->updateOrCreate(
                         [
                             'declaration_type' => 'GENERAL_DECLARATION',
                             'declaration_title' => 'Politically Exposed Person (PEP)'
                         ],
                         [
                             'declaration_text' => 'I declare that I am NOT a politically exposed person or related to one.',
                             'is_accepted' => $request->has('pep'),
                             'is_mandatory' => false,
                         ]
                     );

                     // Residency declaration (mapped to GENERAL_DECLARATION type)
                     $loan->declarations()->updateOrCreate(
                         [
                             'declaration_type' => 'GENERAL_DECLARATION',
                             'declaration_title' => 'Residency Declaration'
                         ],
                         [
                             'declaration_text' => 'I declare that I am a Resident Indian.',
                             'is_accepted' => $request->has('resident'),
                             'is_mandatory' => false,
                         ]
                     );

                     // Credit bureau consent
                     $loan->declarations()->updateOrCreate(
                         [
                             'declaration_type' => 'CIBIL_CONSENT',
                             'declaration_title' => 'Credit Check Consent'
                         ],
                         [
                             'declaration_text' => 'I hereby authorize the bank to access my credit report from CIBIL/Experian for the purpose of this loan application.',
                             'is_accepted' => $request->has('cibil'),
                             'is_mandatory' => true,
                         ]
                     );

                     // Terms & Privacy
                     $loan->declarations()->updateOrCreate(
                         [
                             'declaration_type' => 'TERMS_CONDITIONS',
                             'declaration_title' => 'Terms & Privacy Consent'
                         ],
                         [
                             'declaration_text' => 'I agree to the Terms & Conditions and Privacy Policy of the bank.',
                             'is_accepted' => $request->has('privacy'),
                             'is_mandatory' => true,
                         ]
                     );
                     break;
                
                case 8: // Final Submit
                     $loan->update([
                         'status' => 'SUBMITTED',
                         'submitted_at' => now()
                     ]);
                     // Dispatch background processing job
                     // Ensure dispatch uses database queue connection to avoid Redis
                     ProcessLoanApplication::dispatch($loan)->onConnection('database');
                     break;
            }

            // Update progress if moving forward
            if ($step > ($loan->stage_order ?? 0)) {
                $loan->update(['stage_order' => $step]);
            }
        });

        // Determine Next Step
        $nextStep = $step + 1;
        if ($nextStep > 8) {
            return redirect()->route('loans.show', $loan)->with('success', 'Application Submitted Successfully!');
        }

        return redirect()->route('loans.step.show', ['loan' => $loan->id, 'step' => $nextStep])
                         ->with('success', 'Step saved successfully.');
    }

    public function store(StoreLoanApplicationRequest $request, LoanApplicationService $service): RedirectResponse
    {
        $loan = $service->create($request->user(), $request->validated());

        return redirect()->route('loans.show', $loan)->with('success', 'Application created');
    }

    public function show(LoanApplication $loan): View|RedirectResponse
    {
        Gate::authorize('view', $loan);

        // If the application is in DRAFT, resume at the next pending step
        if (method_exists($loan, 'isDraft') && $loan->isDraft()) {
            $nextStep = ($loan->stage_order ?? 0) + 1;
            return redirect()->route('loans.step.show', ['loan' => $loan->id, 'step' => $nextStep]);
        }

        return view('loans.show', compact('loan'));
    }

    public function edit(LoanApplication $loan): View|RedirectResponse
    {
        Gate::authorize('update', $loan);
        // If the application is still in DRAFT, continue in the step wizard at the next pending step
        if ($loan->isDraft()) {
            $nextStep = ($loan->stage_order ?? 0) + 1;
            return redirect()->route('loans.step.show', ['loan' => $loan->id, 'step' => $nextStep]);
        }

        return view('loans.edit', compact('loan'));
    }

    public function update(UpdateLoanApplicationRequest $request, LoanApplication $loan, LoanApplicationService $service): RedirectResponse
    {
        Gate::authorize('update', $loan);
        $service->update($loan, $request->validated());

        return redirect()->route('loans.show', $loan)->with('success', 'Application updated');
    }

    public function destroy(LoanApplication $loan, LoanApplicationService $service): RedirectResponse
    {
        Gate::authorize('delete', $loan);

        $service->delete($loan);

        return redirect()->route('loans.index')->with('success', 'Application deleted');
    }

    public function saveDraft(LoanApplication $loan): RedirectResponse
    {
        Gate::authorize('update', $loan);

        // Only allow saving when in DRAFT
        if (strtoupper((string) $loan->status) !== 'DRAFT') {
            return back()->with('success', 'Only draft applications can be saved as draft.');
        }

        $loan->update([
            'is_saved' => true,
        ]);

        return redirect()->route('dashboard')->with('success', 'Draft saved to dashboard.');
    }
    public function bulkDestroy(Request $request, LoanApplicationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['uuid'],
        ]);

        $ids = $validated['ids'];

        $loans = LoanApplication::query()
            ->whereIn('id', $ids)
            ->where('user_id', auth()->id())
            ->get();

        $deleted = 0;
        $skipped = [];

        foreach ($loans as $loan) {
            if (Gate::allows('delete', $loan)) {
                $service->delete($loan);
                $deleted++;
            } else {
                $skipped[] = $loan->id;
            }
        }

        $message = $deleted . ' application(s) deleted';
        if (!empty($skipped)) {
            $message .= '. ' . count($skipped) . ' skipped due to permissions/status.';
        }

        return redirect()->route('loans.index')->with('success', $message);
    }

    public function approve(LoanApplication $loan): RedirectResponse
    {
        Gate::authorize('approve', $loan);

        $loan->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Loan approved');
    }
}
