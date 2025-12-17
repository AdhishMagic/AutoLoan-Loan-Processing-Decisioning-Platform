<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @php
                    $user = Auth::user();
                    $isAdmin = $user?->isAdmin();
                    $isOfficer = $user?->isLoanOfficer();
                    $isUser = $user?->isUser();
                    $appsQuery = \App\Models\LoanApplication::query();
                    if ($isUser) {
                        $appsQuery->where('user_id', $user->id);
                    }
                    $totalApps = (clone $appsQuery)->count();
                    $approved = (clone $appsQuery)->where('status', 'approved')->count();
                    $pending = (clone $appsQuery)->where('status', 'pending')->count();
                    $rejected = (clone $appsQuery)->where('status', 'rejected')->count();
                @endphp

                <div class="rounded-lg border bg-white p-4 shadow-sm">
                    <div class="text-sm text-gray-500">Applications</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $totalApps }}</div>
                </div>
                <div class="rounded-lg border bg-white p-4 shadow-sm">
                    <div class="text-sm text-gray-500">Approved</div>
                    <div class="mt-1 text-2xl font-semibold text-emerald-600">{{ $approved }}</div>
                </div>
                <div class="rounded-lg border bg-white p-4 shadow-sm">
                    <div class="text-sm text-gray-500">Pending</div>
                    <div class="mt-1 text-2xl font-semibold text-amber-600">{{ $pending }}</div>
                </div>
                <div class="rounded-lg border bg-white p-4 shadow-sm">
                    <div class="text-sm text-gray-500">Rejected</div>
                    <div class="mt-1 text-2xl font-semibold text-rose-600">{{ $rejected }}</div>
                </div>
            </div>

            <div class="mb-6 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-semibold text-gray-900">{{ $isAdmin ? 'Overview' : 'Your Applications' }}</h2>
                <div class="flex gap-2">
                    @if($isUser)
                        <a href="{{ route('loans.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">New Application</a>
                    @endif
                    @if($isAdmin)
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center rounded-md border px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Manage Users</a>
                    @endif
                </div>
            </div>

            <div class="overflow-hidden rounded-lg border bg-white shadow-sm">
                <div class="relative overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Reference</th>
                                <th class="px-4 py-3">Applicant</th>
                                <th class="px-4 py-3">Amount</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Updated</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y bg-white">
                            @php
                                $recentApps = $appsQuery->with('user')->latest()->limit(8)->get();
                            @endphp
                            @forelse($recentApps as $app)
                                <tr>
                                    <td class="px-4 py-3 font-medium">{{ $app->reference ?? ('LA-'.str_pad($app->id, 6, '0', STR_PAD_LEFT)) }}</td>
                                    <td class="px-4 py-3">{{ $app->user?->name ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ number_format($app->loan_amount ?? 0) }}</td>
                                    <td class="px-4 py-3">
                                        @php($s = strtolower($app->status ?? 'pending'))
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                            {{ $s === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($s === 'rejected' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                            {{ ucfirst($s) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ $app->updated_at?->diffForHumans() }}</td>
                                    <td class="px-4 py-3">
                                        @if($isUser)
                                            <a href="{{ route('loans.show', $app) }}" class="text-indigo-600 hover:underline">View</a>
                                        @elseif($isOfficer)
                                            <a href="{{ route('officer.review') }}" class="text-indigo-600 hover:underline">Review</a>
                                        @else
                                            <a href="#" class="text-indigo-600 hover:underline">Open</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">No applications found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
