<x-wizard-layout :loan="$loan" :step="$step">
    <div class="space-y-6">
        <div class="border-b border-app-border pb-5">
            <h3 class="text-base font-semibold leading-6 text-text-primary">Step 5: Property Details</h3>
            <p class="mt-2 text-sm text-text-secondary">Details of the property to be financed or mortgaged.</p>
            <p class="mt-1 text-sm text-text-secondary">Fields marked <span class="text-status-danger">*</span> are mandatory.</p>
        </div>

        <form method="POST" action="{{ route('loans.step.store', ['loan' => $loan->id, 'step' => 5]) }}" class="space-y-8">
            @csrf

            <!-- Section 1: Property Identification -->
            <div class="bg-app-surface p-4 rounded-lg border border-app-border shadow-sm">
                 <h4 class="text-sm font-bold text-text-primary uppercase tracking-wider mb-4 border-b border-app-border pb-2">Property Information</h4>
                 <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="property_type" class="block text-sm font-medium text-text-primary">Property Type <span class="text-status-danger">*</span></label>
                        <select id="property_type" name="property_type" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                            <option value="">Select property type</option>
                            <option value="RESIDENTIAL_FLAT" @selected(old('property_type')==='RESIDENTIAL_FLAT')>Residential Flat / Apartment</option>
                            <option value="RESIDENTIAL_VILLA" @selected(old('property_type')==='RESIDENTIAL_VILLA')>Residential Villa</option>
                            <option value="RESIDENTIAL_BUNGALOW" @selected(old('property_type')==='RESIDENTIAL_BUNGALOW')>Residential Bungalow</option>
                            <option value="RESIDENTIAL_PLOT" @selected(old('property_type')==='RESIDENTIAL_PLOT')>Residential Plot</option>
                            <option value="COMMERCIAL_OFFICE" @selected(old('property_type')==='COMMERCIAL_OFFICE')>Commercial Office</option>
                            <option value="COMMERCIAL_SHOP" @selected(old('property_type')==='COMMERCIAL_SHOP')>Commercial Shop</option>
                            <option value="COMMERCIAL_PLOT" @selected(old('property_type')==='COMMERCIAL_PLOT')>Commercial Plot</option>
                            <option value="INDUSTRIAL" @selected(old('property_type')==='INDUSTRIAL')>Industrial</option>
                            <option value="AGRICULTURAL" @selected(old('property_type')==='AGRICULTURAL')>Agricultural</option>
                            <option value="MIXED_USE" @selected(old('property_type')==='MIXED_USE')>Mixed Use</option>
                            <option value="OTHER" @selected(old('property_type')==='OTHER')>Other</option>
                        </select>
                    </div>
                     <div class="sm:col-span-3">
                        <label for="construction_status" class="block text-sm font-medium text-text-primary">Construction Status <span class="text-status-danger">*</span></label>
                        <select id="construction_status" name="construction_status" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                            <option value="">Select status</option>
                            <option value="READY_TO_MOVE" @selected(old('construction_status')==='READY_TO_MOVE')>Ready to Move</option>
                            <option value="UNDER_CONSTRUCTION" @selected(old('construction_status')==='UNDER_CONSTRUCTION')>Under Construction</option>
                            <option value="NEW_BOOKING" @selected(old('construction_status')==='NEW_BOOKING')>New Booking</option>
                            <option value="RESALE" @selected(old('construction_status')==='RESALE')>Resale</option>
                        </select>
                    </div>
                    <div class="sm:col-span-3">
                        <label for="ownership_type" class="block text-sm font-medium text-text-primary">Ownership Type <span class="text-status-danger">*</span></label>
                        <select name="ownership_type" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6" required>
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
                            <p class="mt-2 text-sm text-status-danger">{{ $message }}</p>
                        @enderror
                    </div>
                     <div class="sm:col-span-3">
                                <label for="market_value" class="block text-sm font-medium text-text-primary">Market Value (₹) <span class="text-status-danger">*</span></label>
                                 <input id="market_value" type="number" name="market_value" required value="{{ old('market_value') }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                    </div>
                 </div>
            </div>

            <!-- Section 2: Property Address -->
            <div class="bg-app-bg p-4 rounded-lg border border-app-border">
                <h4 class="text-sm font-bold text-text-primary uppercase tracking-wider mb-4 border-b border-app-border pb-2">Property Location</h4>
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-1">
                     <div class="sm:col-span-1">
                        <label class="block text-sm font-medium text-text-primary">Address Line 1</label>
                        <textarea name="property_address" rows="3" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6"></textarea>
                    </div>
                     <div class="grid grid-cols-3 gap-6">
                        <div class="col-span-1">
                             <label class="block text-sm font-medium text-text-primary">City</label>
                             <input type="text" name="property_city" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                        </div>
                        <div class="col-span-1">
                             <label class="block text-sm font-medium text-text-primary">State</label>
                             <input type="text" name="property_state" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                        </div>
                         <div class="col-span-1">
                             <label class="block text-sm font-medium text-text-primary">Pincode</label>
                             <input type="text" name="property_pincode" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                        </div>
                     </div>
                </div>
            </div>

             <div class="flex items-center justify-between pt-6 border-t border-app-border">
                <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => 4]) }}" class="text-sm font-semibold leading-6 text-brand-secondary hover:underline">
                    <span aria-hidden="true">&larr;</span> Back
                </a>
                <button type="submit" class="rounded-md bg-brand-accent px-3 py-2 text-sm font-semibold text-text-onDark shadow-sm hover:bg-brand-accent/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-focus">
                    Save & Continue
                </button>
            </div>
        </form>
    </div>
</x-wizard-layout>
