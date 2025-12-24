<x-wizard-layout :loan="$loan" :step="$step">
    <div class="space-y-6">
        <div class="border-b border-gray-200 pb-5">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Step 1: Loan Overview</h3>
            <p class="mt-2 max-w-4xl text-sm text-gray-500">Let's start with the basics. What kind of loan are you looking for?</p>
            <p class="mt-1 text-sm text-gray-500">Fields marked <span class="text-red-500">*</span> are mandatory.</p>
        </div>

        <form method="POST" action="{{ route('loans.step.store', ['loan' => $loan->id, 'step' => 1]) }}" class="space-y-8">
            @csrf
            
            <!-- Section 1: Product Selection -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h4 class="text-sm font-medium text-gray-900 uppercase tracking-wider mb-4">Product Details</h4>
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="loan_product_type" class="block text-sm font-medium leading-6 text-gray-900">Product Type <span class="text-red-500">*</span></label>
                        <div class="mt-2">
                            <select id="loan_product_type" name="loan_product_type" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option value="HOME_LOAN" {{ old('loan_product_type', $loan->loan_product_type) == 'HOME_LOAN' ? 'selected' : '' }}>Home Loan</option>
                                <option value="AUTO_LOAN" {{ old('loan_product_type', $loan->loan_product_type) == 'AUTO_LOAN' ? 'selected' : '' }}>Auto Loan</option>
                                <option value="PERSONAL_LOAN" {{ old('loan_product_type', $loan->loan_product_type) == 'PERSONAL_LOAN' ? 'selected' : '' }}>Personal Loan</option>
                                <option value="LAP" {{ old('loan_product_type', $loan->loan_product_type) == 'LAP' ? 'selected' : '' }}>Loan Against Property</option>
                            </select>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="loan_purpose" class="block text-sm font-medium leading-6 text-gray-900">Purpose</label>
                        <div class="mt-2">
                            <input type="text" name="loan_purpose" id="loan_purpose" value="{{ old('loan_purpose', $loan->loan_purpose) }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="e.g. Purchase of new car">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Term & Amount -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h4 class="text-sm font-medium text-gray-900 uppercase tracking-wider mb-4">Loan Requirements</h4>
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="requested_amount" class="block text-sm font-medium leading-6 text-gray-900">Requested Amount (₹) <span class="text-red-500">*</span></label>
                        <div class="mt-2 relative rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-gray-500 sm:text-sm">₹</span>
                            </div>
                            <input type="number" name="requested_amount" id="requested_amount" required value="{{ old('requested_amount', $loan->requested_amount) }}" class="block w-full rounded-md border-0 py-1.5 pl-7 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="0.00">
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="requested_tenure_months" class="block text-sm font-medium leading-6 text-gray-900">Tenure (Months) <span class="text-red-500">*</span></label>
                        <div class="mt-2">
                            <input type="number" name="requested_tenure_months" id="requested_tenure_months" required value="{{ old('requested_tenure_months', $loan->requested_tenure_months) }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">12 to 240 months</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-x-6">
                <!-- Next Button (Primary) -->
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Save & Continue
                </button>
            </div>
        </form>
    </div>
</x-wizard-layout>
