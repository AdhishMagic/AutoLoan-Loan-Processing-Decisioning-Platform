<x-wizard-layout :loan="$loan" :step="$step">
    <div class="space-y-6">
        <div class="border-b border-app-border pb-5">
            <h3 class="text-base font-semibold leading-6 text-text-primary">Step 6: References</h3>
            <p class="mt-2 text-sm text-text-secondary">Provide at least one personal reference who can vouch for you.</p>
            <p class="mt-1 text-sm text-text-secondary">Fields marked <span class="text-status-danger">*</span> are optional but helpful.</p>
        </div>

        <form method="POST" action="{{ route('loans.step.store', ['loan' => $loan->id, 'step' => 6]) }}" class="space-y-8">
            @csrf

            <div class="bg-app-surface p-4 rounded-lg border border-app-border shadow-sm">
                <h4 class="text-sm font-bold text-text-primary uppercase tracking-wider mb-4 border-b border-app-border pb-2">Reference 1</h4>
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="ref_1_name" class="block text-sm font-medium text-text-primary">Full Name</label>
                        <input id="ref_1_name" type="text" name="ref_1_name" value="{{ old('ref_1_name') }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="ref_1_relation" class="block text-sm font-medium text-text-primary">Relation</label>
                        <select id="ref_1_relation" name="ref_1_relation" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                            <option value="">Select relation</option>
                            <option value="FATHER" @selected(old('ref_1_relation')==='FATHER')>Father</option>
                            <option value="MOTHER" @selected(old('ref_1_relation')==='MOTHER')>Mother</option>
                            <option value="SPOUSE" @selected(old('ref_1_relation')==='SPOUSE')>Spouse</option>
                            <option value="FRIEND" @selected(old('ref_1_relation')==='FRIEND')>Friend</option>
                            <option value="COLLEAGUE" @selected(old('ref_1_relation')==='COLLEAGUE')>Colleague</option>
                            <option value="NEIGHBOR" @selected(old('ref_1_relation')==='NEIGHBOR')>Neighbor</option>
                            <option value="OTHER" @selected(old('ref_1_relation')==='OTHER')>Other</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="ref_1_mobile" class="block text-sm font-medium text-text-primary">Mobile</label>
                        <input id="ref_1_mobile" type="text" name="ref_1_mobile" value="{{ old('ref_1_mobile') }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                    </div>

                    <div class="sm:col-span-6">
                        <label for="ref_1_address" class="block text-sm font-medium text-text-primary">Address</label>
                        <textarea id="ref_1_address" name="ref_1_address" rows="2" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">{{ old('ref_1_address') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-6 border-t border-app-border">
                <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => 5]) }}" class="text-sm font-semibold leading-6 text-brand-secondary hover:underline">
                    <span aria-hidden="true">&larr;</span> Back
                </a>
                <button type="submit" class="rounded-md bg-brand-accent px-3 py-2 text-sm font-semibold text-text-onDark shadow-sm hover:bg-brand-accent/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-focus">
                    Save & Continue
                </button>
            </div>
        </form>
    </div>
</x-wizard-layout>
