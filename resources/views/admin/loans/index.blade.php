<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">All Applications</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('underwriting.rules.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Underwriting Rules</a>
                <a href="{{ route('officer.review') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Officer Review</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Applications</h3>
                </div>
                <div class="relative overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Applicant</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Amount</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Updated</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                                @php($s = strtoupper((string) $loan->status))
                                <tr class="border-b bg-white">
                                    <td class="px-4 py-3 font-medium">#{{ $loan->id }}</td>
                                    <td class="px-4 py-3">{{ $loan->user?->name ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ $loan->loan_type ?? '—' }}</td>
                                    <td class="px-4 py-3">₹{{ number_format((float) ($loan->requested_amount ?? 0), 2) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $s === 'APPROVED' ? 'bg-emerald-50 text-emerald-700' : ($s === 'REJECTED' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                            {{ ucfirst(strtolower($s)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ $loan->updated_at?->diffForHumans() }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.loans.show', $loan) }}" class="inline-flex items-center rounded-md bg-gray-200 px-3 py-2 text-gray-800 hover:bg-gray-300">Open</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">No applications found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $loans->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
