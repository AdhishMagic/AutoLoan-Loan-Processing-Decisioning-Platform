<x-wizard-layout :loan="$loan" :step="$step">
    <div class="space-y-6">
        <div class="border-b border-app-border pb-5 flex justify-between items-end">
            <div>
                <h3 class="text-base font-semibold leading-6 text-text-primary">Step 2: Applicants</h3>
                <p class="mt-2 text-sm text-text-secondary">Tell us about yourself and any co-applicants.</p>
                <p class="mt-1 text-sm text-text-secondary">Fields marked <span class="text-status-danger">*</span> are mandatory.</p>
            </div>
            <button type="button" class="text-sm text-brand-secondary hover:underline font-medium">+ Add Co-Applicant</button>
        </div>

        @php($primaryApplicant = $loan->primaryApplicant()->first())

        <!-- Applicant Tabs -->
        <div class="hidden sm:block">
            <div class="border-b border-app-border">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    @foreach($loan->applicants as $applicant)
                        <a href="#" class="{{ $applicant->applicant_role === 'PRIMARY' ? 'border-brand-secondary text-text-primary' : 'border-transparent text-text-muted hover:border-app-border hover:text-text-secondary' }} whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">
                            {{ $applicant->first_name ?? 'Primary Applicant' }} ({{ $applicant->applicant_role }})
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        <form method="POST" action="{{ route('loans.step.store', ['loan' => $loan->id, 'step' => 2]) }}" class="space-y-8">
            @csrf
            
            <!-- We are editing the PRIMARY applicant for now -->
            <!-- In a real app, we'd have a hidden field for applicant_id or route param -->
            
            <!-- Section 1: Personal Information -->
            <div class="bg-app-surface p-4 rounded-lg border border-app-border shadow-sm">
                <h4 class="text-sm font-bold text-text-primary uppercase tracking-wider mb-4 border-b border-app-border pb-2">Personal Information</h4>
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="first_name" class="block text-sm font-medium text-text-primary">First Name <span class="text-status-danger">*</span></label>
                        <input id="first_name" type="text" name="first_name" required value="{{ old('first_name', $primaryApplicant?->first_name) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                    </div>
                    <div class="sm:col-span-3">
                        <label for="last_name" class="block text-sm font-medium text-text-primary">Last Name <span class="text-status-danger">*</span></label>
                        <input id="last_name" type="text" name="last_name" required value="{{ old('last_name', $primaryApplicant?->last_name) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="date_of_birth" class="block text-sm font-medium text-text-primary">Date of Birth <span class="text-status-danger">*</span></label>
                        <input id="date_of_birth" type="date" name="date_of_birth" required value="{{ old('date_of_birth', optional($primaryApplicant?->date_of_birth)->format('Y-m-d')) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="gender" class="block text-sm font-medium text-text-primary">Gender <span class="text-status-danger">*</span></label>
                        <select id="gender" name="gender" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                            <option value="">Select</option>
                            <option value="MALE" @selected(old('gender', $primaryApplicant?->gender) === 'MALE')>Male</option>
                            <option value="FEMALE" @selected(old('gender', $primaryApplicant?->gender) === 'FEMALE')>Female</option>
                            <option value="OTHER" @selected(old('gender', $primaryApplicant?->gender) === 'OTHER')>Other</option>
                        </select>
                    </div>
                     <div class="sm:col-span-2">
                        <label for="marital_status" class="block text-sm font-medium text-text-primary">Marital Status <span class="text-status-danger">*</span></label>
                        <select id="marital_status" name="marital_status" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                            <option value="">Select</option>
                            <option value="SINGLE" @selected(old('marital_status', $primaryApplicant?->marital_status) === 'SINGLE')>Single</option>
                            <option value="MARRIED" @selected(old('marital_status', $primaryApplicant?->marital_status) === 'MARRIED')>Married</option>
                            <option value="DIVORCED" @selected(old('marital_status', $primaryApplicant?->marital_status) === 'DIVORCED')>Divorced</option>
                            <option value="WIDOWED" @selected(old('marital_status', $primaryApplicant?->marital_status) === 'WIDOWED')>Widowed</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6 mt-6">
                    <div class="sm:col-span-3">
                        <label for="mobile" class="block text-sm font-medium text-text-primary">Mobile Number <span class="text-status-danger">*</span></label>
                        <input id="mobile" type="tel" name="mobile" required value="{{ old('mobile', $primaryApplicant?->mobile) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                    </div>
                    <div class="sm:col-span-3">
                        <label for="email" class="block text-sm font-medium text-text-primary">Email Address <span class="text-status-danger">*</span></label>
                        <input id="email" type="email" name="email" required value="{{ old('email', $primaryApplicant?->email) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                    </div>
                </div>
            </div>

            <!-- Section 2: KYC Documents -->
            <div class="bg-app-surface p-4 rounded-lg border border-app-border shadow-sm">
                <h4 class="text-sm font-bold text-text-primary uppercase tracking-wider mb-4 border-b border-app-border pb-2">Identity Proofs (KYC)</h4>
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                     <div class="sm:col-span-3">
                        <label for="pan_number" class="block text-sm font-medium text-text-primary">PAN Number <span class="text-status-danger">*</span></label>
                        <input id="pan_number" type="text" name="pan_number" required value="{{ old('pan_number', $primaryApplicant?->pan_number) }}" placeholder="ABCDE1234F" class="mt-2 block w-full uppercase rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border placeholder:text-text-muted focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                    </div>
                    <div class="sm:col-span-3">
                        <label for="aadhaar_number" class="block text-sm font-medium text-text-primary">Aadhaar Number <span class="text-status-danger">*</span></label>
                        <input id="aadhaar_number" type="text" name="aadhaar_number" required value="{{ old('aadhaar_number', $primaryApplicant?->aadhaar_number) }}" placeholder="1234 5678 9012" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border placeholder:text-text-muted focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                    </div>
                </div>
            </div>

            <!-- Section 3: Addresses (Simple Implementation for layout) -->
              <div class="bg-app-surface p-4 rounded-lg border border-app-border shadow-sm">
                 <h4 class="text-sm font-bold text-text-primary uppercase tracking-wider mb-4 border-b border-app-border pb-2">Current Residence</h4>
                 <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-1">
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium text-text-primary">Address Line 1</label>
                        <input type="text" name="current_address_line_1" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                    </div>
                     <div class="grid grid-cols-3 gap-6">
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-text-primary">City</label>
                            <input type="text" name="current_city" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-text-primary">State</label>
                            <input type="text" name="current_state" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                        </div>
                         <div class="col-span-1">
                            <label class="block text-sm font-medium text-text-primary">Pincode</label>
                            <input type="text" name="current_pincode" class="mt-2 block w-full rounded-md border-0 py-1.5 text-text-primary shadow-sm ring-1 ring-inset ring-app-border focus:ring-2 focus:ring-inset focus:ring-brand-secondary sm:text-sm sm:leading-6">
                        </div>
                     </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-6 border-t border-app-border">
                <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => 1]) }}" class="text-sm font-semibold leading-6 text-brand-secondary hover:underline">
                    <span aria-hidden="true">&larr;</span> Back
                </a>
                <button type="submit" class="rounded-md bg-brand-accent px-3 py-2 text-sm font-semibold text-text-onDark shadow-sm hover:bg-brand-accent/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-focus">
                    Save & Next Step
                </button>
            </div>
        </form>
    </div>
</x-wizard-layout>

