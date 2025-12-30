<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-text-primary leading-tight">All Applications</h2>
            <div class="flex items-center gap-3">
                 <a href="{{ route('underwriting.rules.index') }}" class="text-sm text-text-secondary hover:text-text-primary">Underwriting Rules</a>
                 <a href="{{ route('officer.review') }}" class="text-sm text-text-secondary hover:text-text-primary">Officer Review</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-app-surface overflow-hidden shadow-sm sm:rounded-lg p-6 ring-1 ring-app-border">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-text-primary">Applications</h3>
                </div>
                <div class="relative overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-app-hover text-xs uppercase text-text-muted">
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
                                @php
                                    $s = strtoupper((string) ($loan->status ?? 'DRAFT'));
                                    $statusClass = match (true) {
                                        $s === 'APPROVED' => 'bg-status-success/10 text-status-success ring-1 ring-status-success/20',
                                        $s === 'REJECTED' => 'bg-status-danger/10 text-status-danger ring-1 ring-status-danger/20',
                                        in_array($s, ['MANUAL_REVIEW', 'HOLD', 'PENDING'], true) => 'bg-status-warning/10 text-status-warning ring-1 ring-status-warning/20',
                                        default => 'bg-status-info/10 text-status-info ring-1 ring-status-info/20',
                                    };
                                @endphp
                                <tr class="border-b border-app-divider bg-app-surface">
                                    <td class="px-4 py-3 font-medium">#{{ $loan->id }}</td>
                                    <td class="px-4 py-3">{{ $loan->user?->name ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ $loan->loan_type ?? '—' }}</td>
                                    <td class="px-4 py-3">₹{{ number_format((float) ($loan->requested_amount ?? 0), 2) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $statusClass }}">
                                            {{ ucfirst(strtolower($s)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-text-muted">{{ $loan->updated_at?->diffForHumans() }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.loans.show', $loan) }}" class="inline-flex items-center rounded-md bg-app-hover px-3 py-2 text-text-primary ring-1 ring-app-border hover:bg-app-divider">Open</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-text-muted">No applications found.</td>
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
