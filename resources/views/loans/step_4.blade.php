<x-wizard-layout :loan="$loan" :step="$step">
    <div class="space-y-6">
        <div class="border-b border-gray-200 pb-5">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Step 4: Financial Profile</h3>
            <p class="mt-2 text-sm text-gray-500">Provide details of your banking relationships and existing obligations.</p>
            <p class="mt-1 text-sm text-gray-500">Fields marked <span class="text-red-500">*</span> are mandatory.</p>
        </div>

        <form method="POST" action="{{ route('loans.step.store', ['loan' => $loan->id, 'step' => 4]) }}" class="space-y-8">
            @csrf

            <!-- Section 1: Bank Accounts -->
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                 <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-2">Primary Bank Account</h4>
                 <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="bank_name" class="block text-sm font-medium text-gray-900">Bank Name <span class="text-red-500">*</span></label>
                        <input id="bank_name" type="text" name="bank_name" required value="{{ old('bank_name', $loan->primary_bank_name ?? '') }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                     <div class="sm:col-span-3">
                        <label for="account_type" class="block text-sm font-medium text-gray-900">Account Type <span class="text-red-500">*</span></label>
                        <select id="account_type" name="account_type" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="">Select</option>
                            <option value="SAVINGS" @selected(old('account_type', $loan->account_type ?? '') === 'SAVINGS')>Savings</option>
                            <option value="CURRENT" @selected(old('account_type', $loan->account_type ?? '') === 'CURRENT')>Current</option>
                            <option value="SALARY" @selected(old('account_type', $loan->account_type ?? '') === 'SALARY')>Salary</option>
                        </select>
                    </div>
                     <div class="sm:col-span-3">
                        <label for="account_number" class="block text-sm font-medium text-gray-900">Account Number</label>
                        <input id="account_number" type="text" name="account_number" value="{{ old('account_number') }}" placeholder="Only last 4 digits stored securely" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>

                    <div class="sm:col-span-3">
                        <label for="account_holder_name" class="block text-sm font-medium text-gray-900">Account Holder Name <span class="text-red-500">*</span></label>
                        <input id="account_holder_name" type="text" name="account_holder_name" required value="{{ old('account_holder_name', $primaryApplicant->full_name ?? '') }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>

                     <div class="sm:col-span-3">
                        <label for="ifsc_code" class="block text-sm font-medium text-gray-900">IFSC Code</label>
                        <input id="ifsc_code" type="text" name="ifsc_code" value="{{ old('ifsc_code') }}" class="mt-2 block w-full uppercase rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                 </div>
            </div>

            <!-- Section 2: Existing Obligations -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Existing Loans</h4>
                    <button type="button" class="text-xs text-indigo-600 font-semibold">+ Add Another Loan</button>
                </div>
                
                <!-- Mock Repeater Item -->
                <div class="bg-white p-4 rounded border border-gray-200 mb-2">
                     <div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
                        <div class="sm:col-span-2">
                            <label for="existing_loans[0][lender]" class="block text-xs font-medium text-gray-700">Lender Name</label>
                            <input id="existing_loans_0_lender" type="text" name="existing_loans[0][lender]" value="{{ old('existing_loans.0.lender') }}" class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm sm:leading-6">
                        </div>
                         <div class="sm:col-span-2">
                            <label for="existing_loans[0][type]" class="block text-xs font-medium text-gray-700">Product</label>
                            <select id="existing_loans_0_type" name="existing_loans[0][type]" class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm sm:leading-6">
                                <option>Personal Loan</option>
                                <option>Auto Loan</option>
                                <option>Home Loan</option>
                            </select>
                        </div>
                        <div class="sm:col-span-1">
                            <label for="existing_loans[0][emi]" class="block text-xs font-medium text-gray-700">EMI (₹)</label>
                            <input id="existing_loans_0_emi" type="number" name="existing_loans[0][emi]" value="{{ old('existing_loans.0.emi') }}" class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm sm:leading-6">
                        </div>
                         <div class="sm:col-span-1 flex items-center pt-6">
                           <div class="flex items-center gap-x-2">
                                <input type="checkbox" name="existing_loans[0][close]" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label class="text-xs text-gray-600">To be closed?</label>
                           </div>
                        </div>
                     </div>
                </div>
            </div>

             <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => 3]) }}" class="text-sm font-semibold leading-6 text-gray-900">
                    <span aria-hidden="true">&larr;</span> Back
                </a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Save & Continue
                </button>
            </div>
        </form>
    </div>
</x-wizard-layout>