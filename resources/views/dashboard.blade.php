{{-- Page: Authenticated dashboard (/dashboard). Shows application stats and quick actions based on role. --}}
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
                        $appsQuery->where('user_id', $user->id)
                                  ->where(function($q){
                                      $q->where('status', '!=', 'DRAFT')
                                        ->orWhere('is_saved', true);
                                  });
                    } elseif ($isOfficer) {
                        $appsQuery->where('assigned_officer_id', $user->id)
                                 ->whereRaw('UPPER(status) != ?', ['DRAFT']);
                    }
                    $totalApps = (clone $appsQuery)->count();
                    $approved = (clone $appsQuery)->whereRaw('UPPER(status) = ?', ['APPROVED'])->count();
                    $rejected = (clone $appsQuery)->whereRaw('UPPER(status) = ?', ['REJECTED'])->count();
                    $pending = (clone $appsQuery)
                        ->whereRaw('UPPER(status) NOT IN (?, ?, ?)', ['DRAFT', 'APPROVED', 'REJECTED'])
                        ->count();
                @endphp

                <div class="rounded-lg border border-app-border bg-app-surface p-4 shadow-sm">
                    <div class="text-sm text-text-secondary">Applications</div>
                    <div class="mt-1 text-2xl font-semibold text-text-primary">{{ $totalApps }}</div>
                </div>
                <div class="rounded-lg border border-app-border bg-app-surface p-4 shadow-sm">
                    <div class="text-sm text-text-secondary">Approved</div>
                    <div class="mt-1 text-2xl font-semibold text-status-success">{{ $approved }}</div>
                </div>
                <div class="rounded-lg border border-app-border bg-app-surface p-4 shadow-sm">
                    <div class="text-sm text-text-secondary">Pending</div>
                    <div class="mt-1 text-2xl font-semibold text-status-warning">{{ $pending }}</div>
                </div>
                <div class="rounded-lg border border-app-border bg-app-surface p-4 shadow-sm">
                    <div class="text-sm text-text-secondary">Rejected</div>
                    <div class="mt-1 text-2xl font-semibold text-status-danger">{{ $rejected }}</div>
                </div>
            </div>

            <div class="mb-6 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-semibold text-text-primary">{{ $isAdmin ? 'Overview' : 'Your Applications' }}</h2>
                <div class="flex flex-wrap gap-2">
                    @if($isUser || $isAdmin)
                        <a href="{{ route('loans.create') }}" class="inline-flex w-full sm:w-auto items-center justify-center rounded-md bg-brand-accent px-3 py-2 text-sm font-medium text-text-onDark shadow-sm hover:bg-brand-accent/90">New Application</a>
                    @endif
                    @if($isAdmin)
                        <a href="{{ route('admin.users.index') }}" class="inline-flex w-full sm:w-auto items-center justify-center rounded-md bg-app-surface px-3 py-2 text-sm font-medium text-brand-primary ring-1 ring-app-border hover:bg-app-hover hover:text-brand-secondary focus:outline-none focus:ring-2 focus:ring-brand-focus focus:ring-offset-2 focus:ring-offset-app-bg">Manage Users</a>
                        <a href="{{ route('admin.loans.index') }}" class="inline-flex w-full sm:w-auto items-center justify-center rounded-md bg-app-surface px-3 py-2 text-sm font-medium text-brand-primary ring-1 ring-app-border hover:bg-app-hover hover:text-brand-secondary focus:outline-none focus:ring-2 focus:ring-brand-focus focus:ring-offset-2 focus:ring-offset-app-bg">All Applications</a>
                    @endif
                </div>
            </div>

            @if($isUser)
                <div class="mb-6 rounded-md border border-app-border bg-app-bg p-4">
                    <div class="text-sm font-medium text-text-primary">AutoLoan application — required inputs</div>
                    <div class="mt-1 text-sm text-text-secondary">In the application wizard, fields marked <span class="text-status-danger">*</span> are mandatory.</div>
                    <div class="mt-3 grid grid-cols-1 gap-2 text-sm text-text-secondary sm:grid-cols-2">
                        <div>Step 1: Product type, requested amount, tenure</div>
                        <div>Step 2: Name, DOB, PAN, Aadhaar, mobile, email, gender, marital status</div>
                        <div>Step 3: Employment type, gross income</div>
                        <div>Step 7–8: Mandatory consents and required documents</div>
                    </div>
                </div>
            @endif

            <div class="overflow-hidden rounded-lg border border-app-border bg-app-surface shadow-sm">
                <div class="relative overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-app-bg text-xs uppercase text-text-muted">
                            <tr>
                                <th class="px-4 py-3">Reference</th>
                                <th class="px-4 py-3">Applicant</th>
                                <th class="px-4 py-3">Amount</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Updated</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-app-border bg-app-surface">
                            @php
                                $recentApps = $appsQuery->with('user')->latest()->limit(8)->get();
                            @endphp
                            @forelse($recentApps as $app)
                                <tr>
                                    <td class="px-4 py-3 font-medium">{{ $app->reference ?? ('LA-'.str_pad($app->id, 6, '0', STR_PAD_LEFT)) }}</td>
                                    <td class="px-4 py-3">{{ $app->user?->name ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ number_format($app->requested_amount ?? 0) }}</td>
                                    <td class="px-4 py-3">
                                        @php($s = strtolower($app->status ?? 'pending'))
                                        @php($label = \Illuminate\Support\Str::headline($s))
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                            {{ $s === 'approved' ? 'bg-status-success/10 text-status-success' : ($s === 'rejected' ? 'bg-status-danger/10 text-status-danger' : 'bg-status-warning/10 text-status-warning') }}">
                                            {{ $label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-text-muted">{{ $app->updated_at?->diffForHumans() }}</td>
                                    <td class="px-4 py-3">
                                        @if($isUser)
                                            <a href="{{ route('loans.show', $app) }}" class="text-brand-secondary hover:underline">View</a>
                                        @elseif($isOfficer)
                                            <a href="{{ route('officer.loans.show', $app) }}" class="text-brand-secondary hover:underline">Open</a>
                                        @else
                                            <a href="{{ route('admin.loans.show', $app) }}" class="text-brand-secondary hover:underline">Open</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-text-secondary">No applications found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @if($isAdmin)
            <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8 mt-6">
                <div class="rounded-lg border border-app-border bg-app-surface shadow-sm">
                    <div class="px-4 py-3 border-b border-app-border">
                        <h3 class="text-lg font-semibold text-text-primary">Recent Activity</h3>
                        <p class="text-sm text-text-secondary">Latest audit logs across the system</p>
                    </div>
                    @php($logs = \App\Models\AuditLog::query()->with('user')->latest()->limit(10)->get())
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-app-bg text-xs uppercase text-text-muted">
                                <tr>
                                    <th class="px-4 py-3">When</th>
                                    <th class="px-4 py-3">User</th>
                                    <th class="px-4 py-3">Action</th>
                                    <th class="px-4 py-3">IP</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-app-border bg-app-surface">
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="px-4 py-3 text-text-muted">{{ $log->created_at?->diffForHumans() }}</td>
                                        <td class="px-4 py-3">{{ $log->user?->email ?? 'System' }}</td>
                                        <td class="px-4 py-3">{{ $log->action }}</td>
                                        <td class="px-4 py-3 text-text-muted">{{ $log->ip_address ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-text-secondary">No activity yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
