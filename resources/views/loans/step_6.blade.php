<x-wizard-layout :loan="$loan" :step="$step">
    <div class="space-y-6">
        <div class="border-b border-gray-200 pb-5">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Step 6: References</h3>
            <p class="mt-2 text-sm text-gray-500">Please provide two references for verification.</p>
        </div>

        <form method="POST" action="{{ route('loans.step.store', ['loan' => $loan->id, 'step' => 6]) }}" class="space-y-8">
            @csrf

            <!-- Reference 1 -->
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                 <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-2">Reference 1 (Relative)</h4>
                 <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-900">Full Name</label>
                        <input type="text" name="ref_1_name" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                     <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-900">Relationship</label>
                        <select name="ref_1_relation" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option>Father</option>
                            <option>Mother</option>
                            <option>Spouse</option>
                            <option>Sibling</option>
                        </select>
                    </div>
                     <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-900">Mobile Number</label>
                        <input type="tel" name="ref_1_mobile" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                     <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-900">Address / City</label>
                        <input type="text" name="ref_1_address" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                 </div>
            </div>

            <!-- Reference 2 -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                 <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-2">Reference 2 (Friend / Colleague)</h4>
                 <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-900">Full Name</label>
                        <input type="text" name="ref_2_name" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                     <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-900">Relationship</label>
                        <select name="ref_2_relation" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option>Friend</option>
                            <option>Colleague</option>
                            <option>Neighbor</option>
                        </select>
                    </div>
                     <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-900">Mobile Number</label>
                        <input type="tel" name="ref_2_mobile" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                     <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-900">Address / City</label>
                        <input type="text" name="ref_2_address" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                 </div>
            </div>

             <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => 5]) }}" class="text-sm font-semibold leading-6 text-gray-900">
                    <span aria-hidden="true">&larr;</span> Back
                </a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Save & Continue
                </button>
            </div>
        </form>
    </div>
</x-wizard-layout>
