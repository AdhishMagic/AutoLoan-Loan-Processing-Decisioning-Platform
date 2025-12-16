@php($editing = isset($loan))
<div x-data="{ step: 1, maxStep: 4, next(){ if(this.step < this.maxStep) this.step++ }, prev(){ if(this.step>1) this.step-- }, goto(s){ if(s>=1 && s<=this.maxStep) this.step=s } }" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <!-- Left column: step content -->
  <div class="lg:col-span-2 space-y-6">
    <!-- Step 1: Details -->
    <div class="bg-white rounded-lg shadow p-6" x-show="step === 1" x-cloak>
      <h2 class="text-sm font-semibold text-gray-700 mb-4">Loan Details</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-forms.select name="loan_type" label="Loan Type" :options="['HOME'=>'Home Loan','AUTO'=>'Auto Loan','MORTGAGE'=>'Mortgage']" :value="$loan->loan_type ?? null" required />
        <x-forms.input name="requested_amount" label="Requested Amount" type="number" step="0.01" :value="$loan->requested_amount ?? null" required />
        <x-forms.input name="tenure_months" label="Tenure (Months)" type="number" :value="$loan->tenure_months ?? null" required />
      </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6" x-show="step === 1" x-cloak>
      <h2 class="text-sm font-semibold text-gray-700 mb-4">Purpose</h2>
      <x-forms.textarea name="purpose_description" label="Describe purpose (optional)" rows="4" :value="$loan->purpose_description ?? null" />
    </div>

    <!-- Step 2: Applicants (placeholder) -->
    <div class="bg-white rounded-lg shadow p-6" x-show="step === 2" x-cloak>
      <h2 class="text-sm font-semibold text-gray-700 mb-4">Applicants</h2>
      <p class="text-sm text-gray-600">Add applicant details in this step (e.g., primary and co-applicants). This section can be wired to dedicated Applicant CRUD. For now, proceed with Next.</p>
    </div>

    <!-- Step 3: Documents (placeholder) -->
    <div class="bg-white rounded-lg shadow p-6" x-show="step === 3" x-cloak>
      <h2 class="text-sm font-semibold text-gray-700 mb-4">Documents</h2>
      <p class="text-sm text-gray-600">Upload required KYC and income documents here. The timeline logs document uploads and verifications automatically. You can proceed and attach later if not ready.</p>
    </div>

    <!-- Step 4: Review -->
    <div class="bg-white rounded-lg shadow p-6" x-show="step === 4" x-cloak>
      <h2 class="text-sm font-semibold text-gray-700 mb-4">Review & Submit</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
        <div>
          <div class="text-gray-500">Loan Type</div>
          <div class="font-medium">{{ old('loan_type', $loan->loan_type ?? '—') }}</div>
        </div>
        <div>
          <div class="text-gray-500">Requested Amount</div>
          <div class="font-medium">{{ old('requested_amount', $loan->requested_amount ?? '—') }}</div>
        </div>
        <div>
          <div class="text-gray-500">Tenure (Months)</div>
          <div class="font-medium">{{ old('tenure_months', $loan->tenure_months ?? '—') }}</div>
        </div>
      </div>
      <p class="mt-4 text-xs text-gray-500">Submitting will save your application as a draft. You can submit for review later.</p>
    </div>
  </div>

  <!-- Right column: stepper + actions -->
  <div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
      <h3 class="text-sm font-semibold text-gray-700 mb-3">Progress</h3>
      <ol class="space-y-2 text-sm">
        <li>
          <button type="button" @click="goto(1)" class="flex items-center gap-2" :class="step===1 ? 'text-blue-700' : 'text-gray-700'">
            <span class="size-2 rounded-full" :class="step>=1 ? 'bg-blue-600' : 'bg-gray-300'"></span>
            Details
          </button>
        </li>
        <li>
          <button type="button" @click="goto(2)" class="flex items-center gap-2" :class="step===2 ? 'text-blue-700' : 'text-gray-700'">
            <span class="size-2 rounded-full" :class="step>=2 ? 'bg-blue-600' : 'bg-gray-300'"></span>
            Applicants
          </button>
        </li>
        <li>
          <button type="button" @click="goto(3)" class="flex items-center gap-2" :class="step===3 ? 'text-blue-700' : 'text-gray-700'">
            <span class="size-2 rounded-full" :class="step>=3 ? 'bg-blue-600' : 'bg-gray-300'"></span>
            Documents
          </button>
        </li>
        <li>
          <button type="button" @click="goto(4)" class="flex items-center gap-2" :class="step===4 ? 'text-blue-700' : 'text-gray-700'">
            <span class="size-2 rounded-full" :class="step>=4 ? 'bg-blue-600' : 'bg-gray-300'"></span>
            Review & Submit
          </button>
        </li>
      </ol>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <a href="{{ route('loans.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
        </div>
        <div class="flex items-center gap-2">
          <button type="button" @click="prev()" x-show="step>1" class="inline-flex items-center rounded-md bg-white px-4 py-2 text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50" x-cloak>Back</button>
          <button type="button" @click="next()" x-show="step<4" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" x-cloak>Next</button>
          <button type="submit" x-show="step===4" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" x-cloak>{{ $editing ? 'Save Changes' : 'Create Application' }}</button>
        </div>
      </div>
    </div>
  </div>
</div>
