<x-wizard-layout :loan="$loan" :step="$step">
    <div class="space-y-6">
        <div class="border-b border-gray-200 pb-5">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Step 3: Employment & Income</h3>
            <p class="mt-2 text-sm text-gray-500">How do you earn your livelihood? This helps us check eligibility.</p>
            <p class="mt-1 text-sm text-gray-500">Fields marked <span class="text-red-500">*</span> are mandatory.</p>
        </div>

        <form method="POST" action="{{ route('loans.step.store', ['loan' => $loan->id, 'step' => 3]) }}" class="space-y-8">
            @csrf

            <!-- Section 1: Employment Profile -->
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                 <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-2">Employment Information</h4>
                 <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="employment_type" class="block text-sm font-medium text-gray-900">Employment Type <span class="text-red-500">*</span></label>
                        <select id="employment_type" name="employment_type" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
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
                        <label class="block text-sm font-medium text-gray-900">Total Experience (Years)</label>
                        <input type="number" name="total_experience_years" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                      <div class="sm:col-span-6">
                        <label class="block text-sm font-medium text-gray-900">Company / Business Name</label>
                        <input type="text" name="company_name" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                 </div>
            </div>

            <!-- Section 2: Income Details -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-2">Income Declaration</h4>
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                     <div class="sm:col-span-3">
                                <label for="gross_income" class="block text-sm font-medium text-gray-900">Gross Monthly Income (₹) <span class="text-red-500">*</span></label>
                                <input id="gross_income" type="number" name="gross_income" required value="{{ old('gross_income') }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-900">Net Monthly Income (₹)</label>
                        <input type="number" name="net_income" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                         <p class="mt-1 text-xs text-gray-500">Amount credited to bank account per month.</p>
                    </div>
                </div>
            </div>

             <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => 2]) }}" class="text-sm font-semibold leading-6 text-gray-900">
                    <span aria-hidden="true">&larr;</span> Back
                </a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Save & Continue
                </button>
            </div>
        </form>
    </div>
</x-wizard-layout>
