<x-wizard-layout :loan="$loan" :step="$step">
    <div class="space-y-6">
        <div class="border-b border-gray-200 pb-5">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Step 5: Property Details</h3>
            <p class="mt-2 text-sm text-gray-500">Details of the property to be financed or mortgaged.</p>
        </div>

        <form method="POST" action="{{ route('loans.step.store', ['loan' => $loan->id, 'step' => 5]) }}" class="space-y-8">
            @csrf

            <!-- Section 1: Property Identification -->
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                 <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-2">Property Information</h4>
                 <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-900">Property Type</label>
                        <select name="property_type" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option>Apartment / Flat</option>
                            <option>Independent House / Villa</option>
                            <option>Plot / Land</option>
                            <option>Commercial Office</option>
                        </select>
                    </div>
                     <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-900">Construction Status</label>
                        <select name="construction_status" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option>Ready to Move</option>
                            <option>Under Construction</option>
                        </select>
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-900">Ownership Type</label>
                        <select name="ownership_type" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" required>
                            <option value="">Select ownership</option>
                            <option value="SELF" {{ old('ownership_type')==='SELF' ? 'selected' : '' }}>Self</option>
                            <option value="JOINT" {{ old('ownership_type')==='JOINT' ? 'selected' : '' }}>Joint</option>
                            <option value="FAMILY" {{ old('ownership_type')==='FAMILY' ? 'selected' : '' }}>Family</option>
                            <option value="ANCESTRAL" {{ old('ownership_type')==='ANCESTRAL' ? 'selected' : '' }}>Ancestral</option>
                            <option value="COMPANY" {{ old('ownership_type')==='COMPANY' ? 'selected' : '' }}>Company</option>
                            <option value="TRUST" {{ old('ownership_type')==='TRUST' ? 'selected' : '' }}>Trust</option>
                            <option value="OTHER" {{ old('ownership_type')==='OTHER' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('ownership_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                     <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-900">Market Value (₹)</label>
                         <input type="number" name="market_value" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                 </div>
            </div>

            <!-- Section 2: Property Address -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-2">Property Location</h4>
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-1">
                     <div class="sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-900">Address Line 1</label>
                        <textarea name="property_address" rows="3" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                    </div>
                     <div class="grid grid-cols-3 gap-6">
                        <div class="col-span-1">
                             <label class="block text-sm font-medium text-gray-900">City</label>
                             <input type="text" name="property_city" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        <div class="col-span-1">
                             <label class="block text-sm font-medium text-gray-900">State</label>
                             <input type="text" name="property_state" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                         <div class="col-span-1">
                             <label class="block text-sm font-medium text-gray-900">Pincode</label>
                             <input type="text" name="property_pincode" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                     </div>
                </div>
            </div>

             <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => 4]) }}" class="text-sm font-semibold leading-6 text-gray-900">
                    <span aria-hidden="true">&larr;</span> Back
                </a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Save & Continue
                </button>
            </div>
        </form>
    </div>
</x-wizard-layout>
