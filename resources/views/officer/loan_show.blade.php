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
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Applicant</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">Name</div>
                        <div class="font-medium">{{ $primaryApplicant?->first_name }} {{ $primaryApplicant?->last_name }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Email</div>
                        <div class="font-medium">{{ $primaryApplicant?->email ?? $loan->user?->email ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Mobile</div>
                        <div class="font-medium">{{ $primaryApplicant?->mobile ?? '—' }}</div>
                    </div>
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
