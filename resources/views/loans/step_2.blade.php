<x-wizard-layout :loan="$loan" :step="$step">
    <div class="space-y-6">
        <div class="border-b border-gray-200 pb-5 flex justify-between items-end">
            <div>
                <h3 class="text-base font-semibold leading-6 text-gray-900">Step 2: Applicants</h3>
                <p class="mt-2 text-sm text-gray-500">Tell us about yourself and any co-applicants.</p>
                <p class="mt-1 text-sm text-gray-500">Fields marked <span class="text-red-500">*</span> are mandatory.</p>
            </div>
            <button type="button" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">+ Add Co-Applicant</button>
        </div>

        @php($primaryApplicant = $loan->primaryApplicant()->first())

        <!-- Applicant Tabs -->
        <div class="hidden sm:block">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    @foreach($loan->applicants as $applicant)
                        <a href="#" class="{{ $applicant->applicant_role === 'PRIMARY' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">
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
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-2">Personal Information</h4>
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="first_name" class="block text-sm font-medium text-gray-900">First Name <span class="text-red-500">*</span></label>
                        <input id="first_name" type="text" name="first_name" required value="{{ old('first_name', $primaryApplicant?->first_name) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    <div class="sm:col-span-3">
                        <label for="last_name" class="block text-sm font-medium text-gray-900">Last Name <span class="text-red-500">*</span></label>
                        <input id="last_name" type="text" name="last_name" required value="{{ old('last_name', $primaryApplicant?->last_name) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-900">Date of Birth <span class="text-red-500">*</span></label>
                        <input id="date_of_birth" type="date" name="date_of_birth" required value="{{ old('date_of_birth', optional($primaryApplicant?->date_of_birth)->format('Y-m-d')) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="gender" class="block text-sm font-medium text-gray-900">Gender <span class="text-red-500">*</span></label>
                        <select id="gender" name="gender" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="">Select</option>
                            <option value="MALE" @selected(old('gender', $primaryApplicant?->gender) === 'MALE')>Male</option>
                            <option value="FEMALE" @selected(old('gender', $primaryApplicant?->gender) === 'FEMALE')>Female</option>
                            <option value="OTHER" @selected(old('gender', $primaryApplicant?->gender) === 'OTHER')>Other</option>
                        </select>
                    </div>
                     <div class="sm:col-span-2">
                        <label for="marital_status" class="block text-sm font-medium text-gray-900">Marital Status <span class="text-red-500">*</span></label>
                        <select id="marital_status" name="marital_status" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
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
                        <label for="mobile" class="block text-sm font-medium text-gray-900">Mobile Number <span class="text-red-500">*</span></label>
                        <input id="mobile" type="tel" name="mobile" required value="{{ old('mobile', $primaryApplicant?->mobile) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    <div class="sm:col-span-3">
                        <label for="email" class="block text-sm font-medium text-gray-900">Email Address <span class="text-red-500">*</span></label>
                        <input id="email" type="email" name="email" required value="{{ old('email', $primaryApplicant?->email) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
            </div>

            <!-- Section 2: KYC Documents -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-2">Identity Proofs (KYC)</h4>
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                     <div class="sm:col-span-3">
                        <label for="pan_number" class="block text-sm font-medium text-gray-900">PAN Number <span class="text-red-500">*</span></label>
                        <input id="pan_number" type="text" name="pan_number" required value="{{ old('pan_number', $primaryApplicant?->pan_number) }}" placeholder="ABCDE1234F" class="mt-2 block w-full uppercase rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    <div class="sm:col-span-3">
                        <label for="aadhaar_number" class="block text-sm font-medium text-gray-900">Aadhaar Number <span class="text-red-500">*</span></label>
                        <input id="aadhaar_number" type="text" name="aadhaar_number" required value="{{ old('aadhaar_number', $primaryApplicant?->aadhaar_number) }}" placeholder="1234 5678 9012" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
            </div>

            <!-- Section 3: Addresses (Simple Implementation for layout) -->
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b pb-2">Current Residence</h4>
                 <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-1">
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-900">Address Line 1</label>
                        <input type="text" name="current_address_line_1" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                     <div class="grid grid-cols-3 gap-6">
                        <div class="col-span-1">
                             <label class="block text-sm font-medium text-gray-900">City</label>
                             <input type="text" name="current_city" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        <div class="col-span-1">
                             <label class="block text-sm font-medium text-gray-900">State</label>
                             <input type="text" name="current_state" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                         <div class="col-span-1">
                             <label class="block text-sm font-medium text-gray-900">Pincode</label>
                             <input type="text" name="current_pincode" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                     </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => 1]) }}" class="text-sm font-semibold leading-6 text-gray-900">
                    <span aria-hidden="true">&larr;</span> Back
                </a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Save & Next Step
                </button>
            </div>
        </form>
    </div>
</x-wizard-layout>
