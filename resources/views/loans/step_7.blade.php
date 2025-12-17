<x-wizard-layout :loan="$loan" :step="$step">
    <div class="space-y-6">
        <div class="border-b border-gray-200 pb-5">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Step 7: Declarations</h3>
            <p class="mt-2 text-sm text-gray-500">Legal consents and declarations required for processing.</p>
        </div>

        <form method="POST" action="{{ route('loans.step.store', ['loan' => $loan->id, 'step' => 7]) }}" class="space-y-8">
            @csrf

            <!-- Declarations List -->
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Legal Disclaimer</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>By submitting this application, you are authorizing the bank to verify all details provided.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="relative flex items-start">
                    <div class="flex h-6 items-center">
                        <input id="pep" name="pep" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                    </div>
                    <div class="ml-3 text-sm leading-6">
                        <label for="pep" class="font-medium text-gray-900">Politically Exposed Person (PEP)</label>
                        <p class="text-gray-500">I declare that I am NOT a politically exposed person or related to one.</p>
                    </div>
                </div>

                <div class="relative flex items-start">
                    <div class="flex h-6 items-center">
                        <input id="resident" name="resident" type="checkbox" checked class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                    </div>
                    <div class="ml-3 text-sm leading-6">
                        <label for="resident" class="font-medium text-gray-900">Residency</label>
                        <p class="text-gray-500">I declare that I am a Resident Indian.</p>
                    </div>
                </div>

                <div class="relative flex items-start">
                    <div class="flex h-6 items-center">
                        <input id="cibil" name="cibil" type="checkbox" required class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                    </div>
                    <div class="ml-3 text-sm leading-6">
                        <label for="cibil" class="font-medium text-gray-900">Credit Check Consent <span class="text-red-500">*</span></label>
                        <p class="text-gray-500">I hereby authorize the bank to access my credit report from CIBIL/Experian for the purpose of this loan application.</p>
                    </div>
                </div>

                <div class="relative flex items-start">
                    <div class="flex h-6 items-center">
                        <input id="privacy" name="privacy" type="checkbox" required class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                    </div>
                    <div class="ml-3 text-sm leading-6">
                        <label for="privacy" class="font-medium text-gray-900">Privacy Policy <span class="text-red-500">*</span></label>
                        <p class="text-gray-500">I agree to the Terms & Conditions and Privacy Policy of the bank.</p>
                    </div>
                </div>
            </div>

             <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => 6]) }}" class="text-sm font-semibold leading-6 text-gray-900">
                    <span aria-hidden="true">&larr;</span> Back
                </a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Save & Review
                </button>
            </div>
        </form>
    </div>
</x-wizard-layout>
