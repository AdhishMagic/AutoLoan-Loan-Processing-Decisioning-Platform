<x-wizard-layout :loan="$loan" :step="$step">
    <div class="space-y-6">
        <div class="border-b border-app-border pb-5">
            <h3 class="text-base font-semibold leading-6 text-text-primary">Step 3: Employment & Income</h3>
            <p class="mt-2 text-sm text-text-secondary">How do you earn your livelihood? This helps us check eligibility.</p>
            <p class="mt-1 text-sm text-text-secondary">Fields marked <span class="text-status-danger">*</span> are mandatory.</p>
        </div>

        <form method="POST" action="{{ route('loans.step.store', ['loan' => $loan->id, 'step' => 3]) }}" class="space-y-8">
            @csrf

            <!-- Section 1: Employment Profile -->
            <div class="bg-app-surface p-4 rounded-lg border border-app-border shadow-sm">
                 <h4 class="text-sm font-bold text-text-primary uppercase tracking-wider mb-4 border-b border-app-border pb-2">Employment Information</h4>
                 <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="employment_type" class="block text-sm font-medium text-text-primary">Employment Type <span class="text-status-danger">*</span></label>
                        <select id="employment_type" name="employment_type" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                            <option value="">Select</option>
                            <option value="SALARIED" @selected(old('employment_type') === 'SALARIED')>Salaried</option>
                            <option value="SELF_EMPLOYED_BUSINESS" @selected(old('employment_type') === 'SELF_EMPLOYED_BUSINESS')>Self-Employed Business</option>
                            <option value="SELF_EMPLOYED_PROFESSIONAL" @selected(old('employment_type') === 'SELF_EMPLOYED_PROFESSIONAL')>Self-Employed Professional</option>
                            <option value="RETIRED" @selected(old('employment_type') === 'RETIRED')>Retired</option>
                            <option value="UNEMPLOYED" @selected(old('employment_type') === 'UNEMPLOYED')>Unemployed</option>
                            <option value="STUDENT" @selected(old('employment_type') === 'STUDENT')>Student</option>
                            <option value="HOMEMAKER" @selected(old('employment_type') === 'HOMEMAKER')>Homemaker</option>
                        </select>
                    </div>
                     <div class="sm:col-span-3">
                                                <label class="block text-sm font-medium text-text-primary">Total Experience (Years)</label>
                                                <input type="number" name="total_experience_years" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                    </div>
                      <div class="sm:col-span-6">
                                                <label class="block text-sm font-medium text-text-primary">Company / Business Name</label>
                                                <input type="text" name="company_name" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                    </div>
                 </div>
            </div>

            <!-- Section 2: Income Details -->
            <div class="bg-app-bg p-4 rounded-lg border border-app-border">
                <h4 class="text-sm font-bold text-text-primary uppercase tracking-wider mb-4 border-b border-app-border pb-2">Income Declaration</h4>
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                     <div class="sm:col-span-3">
                                <label for="gross_income" class="block text-sm font-medium text-text-primary">Gross Monthly Income (₹) <span class="text-status-danger">*</span></label>
                                <input id="gross_income" type="number" name="gross_income" required value="{{ old('gross_income') }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-text-primary">Net Monthly Income (₹)</label>
                        <input type="number" name="net_income" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                         <p class="mt-1 text-xs text-text-muted">Amount credited to bank account per month.</p>
                    </div>
                </div>
            </div>

             <div class="flex items-center justify-between pt-6 border-t border-app-border">
                <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => 2]) }}" class="text-sm font-semibold leading-6 text-brand-secondary hover:underline">
                    <span aria-hidden="true">&larr;</span> Back
                </a>
                <button type="submit" class="rounded-md bg-brand-accent px-3 py-2 text-sm font-semibold text-text-onDark shadow-sm hover:bg-brand-accent/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-focus">
                    Save & Continue
                </button>
            </div>
        </form>
    </div>
</x-wizard-layout>
