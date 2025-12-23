<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Loan Application Details
            </h2>
            <a href="{{ route('officer.review') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Back to Review</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500">Reference</div>
                        <div class="text-lg font-semibold">{{ $loan->application_number ?? $loan->id }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Status</div>
                        <div class="font-semibold">{{ strtoupper((string) $loan->status) }}</div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">Requested Amount</div>
                        <div class="font-medium">₹{{ number_format((float) ($loan->requested_amount ?? 0), 2) }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Tenure</div>
                        <div class="font-medium">{{ $loan->requested_tenure_months ?? $loan->tenure_months ?? '—' }} months</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Submitted At</div>
                        <div class="font-medium">{{ $loan->submitted_at?->format('Y-m-d H:i') ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Loan Product</div>
                        <div class="font-medium">{{ $loan->loan_product_type ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Loan Purpose</div>
                        <div class="font-medium">{{ $loan->loan_purpose ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Monthly Income (Declared)</div>
                        <div class="font-medium">{{ $loan->monthly_income !== null ? '₹'.number_format((float) $loan->monthly_income, 2) : '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Applicant (User-filled Details)</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">Name</div>
                        <div class="font-medium">{{ $primaryApplicant?->full_name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Email</div>
                        <div class="font-medium">{{ $primaryApplicant?->email ?? $loan->user?->email ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Mobile</div>
                        <div class="font-medium">{{ $primaryApplicant?->mobile ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">DOB</div>
                        <div class="font-medium">{{ $primaryApplicant?->date_of_birth?->format('Y-m-d') ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">PAN</div>
                        <div class="font-medium">{{ $primaryApplicant?->pan_number ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Aadhaar</div>
                        @php
                            $aadhaar = $primaryApplicant?->aadhaar_number;
                            $aadhaarMasked = is_string($aadhaar) && strlen(preg_replace('/\D+/', '', $aadhaar) ?? '') >= 4
                                ? str_repeat('X', max(0, strlen(preg_replace('/\D+/', '', $aadhaar) ?? '') - 4)).substr(preg_replace('/\D+/', '', $aadhaar) ?? '', -4)
                                : $aadhaar;
                        @endphp
                        <div class="font-medium">{{ $aadhaarMasked ?: '—' }}</div>
                    </div>
                </div>

                @php
                    $addresses = $primaryApplicant?->addresses ?? collect();
                @endphp

                <div class="mt-6">
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Addresses</h4>
                    @if($addresses->isEmpty())
                        <div class="text-sm text-gray-500">—</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-600">
                                    <tr>
                                        <th class="py-2 pr-4">Type</th>
                                        <th class="py-2 pr-4">Address</th>
                                        <th class="py-2 pr-4">Pincode</th>
                                        <th class="py-2 pr-4">Verified</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach($addresses as $addr)
                                        <tr>
                                            <td class="py-2 pr-4 font-medium">{{ $addr->address_type ?? '—' }}</td>
                                            <td class="py-2 pr-4">{{ $addr->full_address ?? '—' }}</td>
                                            <td class="py-2 pr-4">{{ $addr->pincode ?? '—' }}</td>
                                            <td class="py-2 pr-4">{{ $addr->is_verified ? 'Yes' : 'No' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-2">Employment</h4>
                        @php
                            $employments = $primaryApplicant?->employmentDetails ?? collect();
                        @endphp
                        @if($employments->isEmpty())
                            <div class="text-sm text-gray-500">—</div>
                        @else
                            <div class="space-y-3">
                                @foreach($employments as $emp)
                                    <div class="rounded-lg border p-3">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $emp->company_name ?? '—' }}</div>
                                                <div class="text-sm text-gray-600">{{ $emp->designation ?? '—' }} • {{ $emp->employment_type ?? '—' }}</div>
                                            </div>
                                            <div class="text-sm text-gray-600">{{ $emp->employment_status ?? '—' }}</div>
                                        </div>
                                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                            <div><span class="text-gray-500">DOJ:</span> {{ $emp->date_of_joining?->format('Y-m-d') ?? '—' }}</div>
                                            <div><span class="text-gray-500">Office Email:</span> {{ $emp->office_email ?? '—' }}</div>
                                            <div><span class="text-gray-500">Office Phone:</span> {{ $emp->office_phone ?? '—' }}</div>
                                            <div><span class="text-gray-500">Verified:</span> {{ $emp->is_verified ? 'Yes' : 'No' }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-2">Income</h4>
                        @php
                            $incomes = $primaryApplicant?->incomeDetails ?? collect();
                        @endphp
                        @if($incomes->isEmpty())
                            <div class="text-sm text-gray-500">—</div>
                        @else
                            <div class="space-y-3">
                                @foreach($incomes as $inc)
                                    <div class="rounded-lg border p-3">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $inc->income_type ?? '—' }} ({{ $inc->income_frequency ?? '—' }})</div>
                                                <div class="text-sm text-gray-600">Reference: {{ $inc->reference_month ?? '—' }} {{ $inc->reference_year ?? '' }}</div>
                                            </div>
                                            <div class="text-sm text-gray-600">Verified: {{ $inc->is_verified ? 'Yes' : 'No' }}</div>
                                        </div>
                                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                            <div><span class="text-gray-500">Net Income:</span> {{ $inc->net_income_amount !== null ? '₹'.number_format((float) $inc->net_income_amount, 2) : '—' }}</div>
                                            <div><span class="text-gray-500">Gross Income:</span> {{ $inc->gross_income_amount !== null ? '₹'.number_format((float) $inc->gross_income_amount, 2) : '—' }}</div>
                                            <div><span class="text-gray-500">Deductions:</span> {{ $inc->deductions_amount !== null ? '₹'.number_format((float) $inc->deductions_amount, 2) : '—' }}</div>
                                            <div><span class="text-gray-500">Salary Mode:</span> {{ $inc->salary_mode ?? '—' }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-6">
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Bank Accounts</h4>
                    @php
                        $bankAccounts = $primaryApplicant?->bankAccounts ?? collect();
                    @endphp
                    @if($bankAccounts->isEmpty())
                        <div class="text-sm text-gray-500">—</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-600">
                                    <tr>
                                        <th class="py-2 pr-4">Bank</th>
                                        <th class="py-2 pr-4">IFSC</th>
                                        <th class="py-2 pr-4">Account</th>
                                        <th class="py-2 pr-4">Type</th>
                                        <th class="py-2 pr-4">Primary</th>
                                        <th class="py-2 pr-4">Salary</th>
                                        <th class="py-2 pr-4">Verified</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach($bankAccounts as $acct)
                                        <tr>
                                            <td class="py-2 pr-4 font-medium">{{ $acct->bank_name ?? '—' }}</td>
                                            <td class="py-2 pr-4">{{ $acct->ifsc_code ?? '—' }}</td>
                                            <td class="py-2 pr-4">{{ $acct->masked_account_number ?? '—' }}</td>
                                            <td class="py-2 pr-4">{{ $acct->account_type ?? '—' }}</td>
                                            <td class="py-2 pr-4">{{ $acct->is_primary_account ? 'Yes' : 'No' }}</td>
                                            <td class="py-2 pr-4">{{ $acct->is_salary_account ? 'Yes' : 'No' }}</td>
                                            <td class="py-2 pr-4">{{ $acct->is_verified ? 'Yes' : 'No' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Documents</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-gray-600">
                            <tr>
                                <th class="py-2 pr-4">Type</th>
                                <th class="py-2 pr-4">File</th>
                                <th class="py-2 pr-4">Uploaded By</th>
                                <th class="py-2 pr-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($documents as $doc)
                                <tr>
                                    <td class="py-2 pr-4 font-medium">{{ strtoupper(str_replace('_', ' ', (string) $doc->document_type)) }}</td>
                                    <td class="py-2 pr-4">{{ $doc->original_name }}</td>
                                    <td class="py-2 pr-4">{{ $doc->user?->email ?? '—' }}</td>
                                    <td class="py-2 pr-4">
                                        @can('view', $doc)
                                            <a class="inline-flex items-center rounded-md bg-blue-600 px-3 py-1.5 text-white hover:bg-blue-700" href="{{ route('loan.document.signed-link', $doc) }}">Download</a>
                                            <a class="ml-2 inline-flex items-center rounded-md bg-gray-200 px-3 py-1.5 text-gray-800 hover:bg-gray-300" target="_blank" href="{{ route('loan.document.signed-link', $doc) }}?json=1">Get link</a>
                                        @else
                                            <span class="text-gray-500">No access</span>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-gray-500">No documents uploaded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Document Verification (OCR + Extracted Data)</h3>

                @if($documents->isEmpty())
                    <div class="text-sm text-gray-500">No documents uploaded.</div>
                @else
                    <div class="space-y-4">
                        @foreach($documents as $doc)
                            <div class="rounded-lg border p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ strtoupper(str_replace('_', ' ', (string) $doc->document_type)) }}</div>
                                        <div class="text-sm text-gray-600">File: {{ $doc->original_name ?? '—' }}</div>
                                        <div class="text-sm text-gray-600">Analyzed: {{ $doc->analyzed_at?->format('Y-m-d H:i') ?? 'Pending' }}</div>
                                    </div>

                                    <div class="text-sm text-gray-700">
                                        <div><span class="text-gray-500">Trust:</span> {{ $doc->trust_score !== null ? $doc->trust_score.'%' : '—' }}</div>
                                        <div><span class="text-gray-500">Authenticity:</span> {{ $doc->authenticity_score !== null ? $doc->authenticity_score.'%' : '—' }}</div>
                                        <div><span class="text-gray-500">Uniqueness:</span> {{ $doc->uniqueness_score !== null ? $doc->uniqueness_score.'%' : '—' }}</div>
                                    </div>
                                </div>

                                <div class="mt-3 grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 mb-1">Extracted Fields</div>
                                        <pre class="text-xs bg-gray-50 border rounded p-3 overflow-x-auto">{{ json_encode($doc->extracted_data ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 mb-1">Verification Result</div>
                                        <pre class="text-xs bg-gray-50 border rounded p-3 overflow-x-auto">{{ json_encode($doc->verification_result ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <details class="rounded border bg-white">
                                        <summary class="cursor-pointer select-none px-3 py-2 text-sm font-semibold text-gray-900">Raw OCR Text</summary>
                                        <div class="px-3 pb-3">
                                            <pre class="text-xs bg-gray-50 border rounded p-3 overflow-x-auto max-h-64">{{ $doc->ocr_text ?? '—' }}</pre>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">References</h3>
                @php
                    $refs = $loan->references ?? collect();
                @endphp
                @if($refs->isEmpty())
                    <div class="text-sm text-gray-500">—</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left text-gray-600">
                                <tr>
                                    <th class="py-2 pr-4">Name</th>
                                    <th class="py-2 pr-4">Relationship</th>
                                    <th class="py-2 pr-4">Mobile</th>
                                    <th class="py-2 pr-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($refs as $ref)
                                    <tr>
                                        <td class="py-2 pr-4 font-medium">{{ $ref->full_name ?? '—' }}</td>
                                        <td class="py-2 pr-4">{{ $ref->relationship ?? '—' }}</td>
                                        <td class="py-2 pr-4">{{ $ref->mobile ?? '—' }}</td>
                                        <td class="py-2 pr-4">{{ $ref->verification_status ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Declarations</h3>
                @php
                    $decls = $loan->declarations ?? collect();
                @endphp
                @if($decls->isEmpty())
                    <div class="text-sm text-gray-500">—</div>
                @else
                    <div class="space-y-3">
                        @foreach($decls as $d)
                            <div class="rounded-lg border p-3">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $d->declaration_title ?? $d->declaration_type ?? 'Declaration' }}</div>
                                        <div class="text-sm text-gray-600">Mandatory: {{ $d->is_mandatory ? 'Yes' : 'No' }}</div>
                                    </div>
                                    <div class="text-sm text-gray-700">Accepted: {{ $d->is_accepted ? 'Yes' : 'No' }}</div>
                                </div>
                                @if(!empty($d->declaration_text))
                                    <div class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $d->declaration_text }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('loans.approve', $loan) }}">
                        @csrf
                        <x-primary-button type="submit">Approve</x-primary-button>
                    </form>
                    <form method="POST" action="{{ route('loans.reject', $loan) }}">
                        @csrf
                        <x-danger-button type="submit">Reject</x-danger-button>
                    </form>
                    <form method="POST" action="{{ route('loans.hold', $loan) }}">
                        @csrf
                        <x-secondary-button type="submit">Hold</x-secondary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
