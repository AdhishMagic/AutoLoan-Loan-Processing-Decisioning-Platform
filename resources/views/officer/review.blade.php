<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text-primary leading-tight">
            {{ __('Officer Review') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-app-surface border border-app-border overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-text-primary">Under Review Applications</h3>
                </div>

                <div class="relative overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-app-bg text-xs uppercase text-text-muted">
                            <tr>
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Applicant</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Amount</th>
                                <th class="px-4 py-3">Submitted</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                                <tr class="border-b border-app-border bg-app-surface">
                                    <td class="px-4 py-3 font-medium">#{{ $loan->id }}</td>
                                    <td class="px-4 py-3">
                                        {{ trim((string) optional($loan->primaryApplicant()->first())->first_name.' '.(string) optional($loan->primaryApplicant()->first())->last_name) ?: ($loan->user?->name ?? '—') }}
                                    </td>
                                    <td class="px-4 py-3">{{ $loan->loan_type }}</td>
                                    <td class="px-4 py-3">₹{{ number_format((float) ($loan->requested_amount ?? 0), 2) }}</td>
                                    <td class="px-4 py-3">{{ optional($loan->submitted_at)->format('Y-m-d') ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('officer.loans.show', $loan) }}" class="inline-flex items-center rounded-md bg-brand-secondary/10 px-3 py-2 text-brand-secondary hover:bg-brand-secondary/15">
                                                View
                                            </a>
                                            <a target="_blank" href="{{ route('officer.loans.decision', $loan) }}" class="inline-flex items-center rounded-md bg-brand-secondary/10 text-brand-secondary px-3 py-2 hover:bg-brand-secondary/15">
                                                Decision
                                             </a>
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
                                                <x-secondary-button type="submit" class="bg-status-warning text-text-onDark border-transparent hover:bg-status-warning/90">Hold</x-secondary-button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-text-secondary">No applications pending review.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $loans->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>