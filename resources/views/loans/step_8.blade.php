<x-wizard-layout :loan="$loan" :step="$step">
    <div class="space-y-6">
        <div class="border-b border-gray-200 pb-5">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Step 8: Review & Submit</h3>
            <p class="mt-2 text-sm text-gray-500">Please review all details carefully before final submission.</p>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 flex">
             <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Final Verification</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Once submitted, you will not be able to change these details without contacting support.</p>
                </div>
            </div>
        </div>

        <dl class="divide-y divide-gray-100">
            <!-- Review Section 1 -->
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Loan Details</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    {{ $loan->loan_product_type }} - ₹{{ number_format($loan->requested_amount) }} for {{ $loan->requested_tenure_months }} months
                    <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => 1]) }}" class="float-right text-indigo-600 hover:text-indigo-900">Edit</a>
                </dd>
            </div>
            
            <!-- Review Section 2 -->
             <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Primary Applicant</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    Applicant Name (Primary)
                     <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => 2]) }}" class="float-right text-indigo-600 hover:text-indigo-900">Edit</a>
                </dd>
            </div>

            <!-- Review Section 3 -->
             <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Declarations</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>CIBIL Check: Authorized</li>
                        <li>Privacy Policy: Accepted</li>
                    </ul>
                     <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => 7]) }}" class="float-right text-indigo-600 hover:text-indigo-900">Edit</a>
                </dd>
            </div>
        </dl>

        <form method="POST" action="{{ route('loans.step.store', ['loan' => $loan->id, 'step' => 8]) }}" class="space-y-8 pt-8 border-t">
            @csrf
            
            <div class="flex items-center justify-between">
                <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => 7]) }}" class="text-sm font-semibold leading-6 text-gray-900">
                    <span aria-hidden="true">&larr;</span> Back
                </a>
                <button type="submit" onclick="return confirm('Are you sure you want to submit this application?')" class="rounded-md bg-green-600 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                    Submit Application
                </button>
            </div>
        </form>
    </div>
</x-wizard-layout>
