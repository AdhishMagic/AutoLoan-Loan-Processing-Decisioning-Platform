<x-wizard-layout :loan="$loan" :step="$step">
    <div class="space-y-6">
        <div class="border-b border-gray-200 pb-5">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Step 7: Declarations & Consents</h3>
            <p class="mt-2 text-sm text-gray-500">Please confirm the declarations and provide consent where required.</p>
            <p class="mt-1 text-sm text-gray-500">Fields marked <span class="text-red-500">*</span> are mandatory.</p>
        </div>

        <form method="POST" action="{{ route('loans.step.store', ['loan' => $loan->id, 'step' => 7]) }}" class="space-y-8">
            @csrf

            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-2">Declarations</h4>

                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <input id="pep" name="pep" type="checkbox" @checked(old('pep')) class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                        <label for="pep" class="text-sm text-gray-700">I confirm I am not a Politically Exposed Person (PEP).</label>
                    </div>

                    <div class="flex items-start gap-3">
                        <input id="resident" name="resident" type="checkbox" @checked(old('resident')) class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                        <label for="resident" class="text-sm text-gray-700">I confirm that I am a Resident Indian.</label>
                    </div>

                    <div class="flex items-start gap-3">
                        <input id="cibil" name="cibil" type="checkbox" @checked(old('cibil')) class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                        <label for="cibil" class="text-sm text-gray-700">I authorize the bank to perform a credit check (CIBIL/Experian). <span class="text-red-500">*</span></label>
                    </div>

                    <div class="flex items-start gap-3">
                        <input id="privacy" name="privacy" type="checkbox" @checked(old('privacy')) class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                        <label for="privacy" class="text-sm text-gray-700">I agree to the Terms & Conditions and Privacy Policy. <span class="text-red-500">*</span></label>
                    </div>

                </div>
            </div>

            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => 6]) }}" class="text-sm font-semibold leading-6 text-gray-900">
                    <span aria-hidden="true">&larr;</span> Back
                </a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Save & Continue
                </button>
            </div>
        </form>
    </div>
</x-wizard-layout>
