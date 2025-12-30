<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text-primary leading-tight">
            Underwriting Rules
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-app-surface overflow-hidden shadow-sm sm:rounded-lg p-6 ring-1 ring-app-border">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-sm text-text-secondary">Manage JSON rule sets used for automated underwriting.</div>
                    @php
                        $isAdmin = auth()->check() && auth()->user()->role?->name === 'admin';
                    @endphp
                    @if(!$isAdmin)
                        <a class="inline-flex items-center rounded-md bg-app-hover px-3 py-2 text-text-primary ring-1 ring-app-border hover:bg-app-divider"
                           href="{{ route('officer.underwriting.rules.create') }}">New Rule</a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-text-secondary">
                            <tr>
                                <th class="py-2 pr-4">ID</th>
                                <th class="py-2 pr-4">Name</th>
                                <th class="py-2 pr-4">Active</th>
                                <th class="py-2 pr-4">Updated</th>
                                <th class="py-2 pr-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-app-divider">
                            @forelse($rules as $rule)
                                <tr>
                                    <td class="py-2 pr-4 font-medium">{{ $rule->id }}</td>
                                    <td class="py-2 pr-4">{{ $rule->name }}</td>
                                    <td class="py-2 pr-4">
                                        @if($rule->active)
                                            <span class="inline-flex items-center rounded-full bg-status-success/10 px-2 py-0.5 text-xs font-medium text-status-success ring-1 ring-status-success/20">Active</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-app-hover px-2 py-0.5 text-xs font-medium text-text-secondary ring-1 ring-app-border">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="py-2 pr-4">{{ $rule->updated_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                    <td class="py-2 pr-4">
                                        <div class="flex items-center gap-2">
                                            @php
                                                $isAdmin = auth()->check() && auth()->user()->role?->name === 'admin';
                                                $editRoute = $isAdmin ? 'underwriting.rules.edit' : 'officer.underwriting.rules.edit';
                                                $activateRoute = $isAdmin ? 'underwriting.rules.activate' : 'officer.underwriting.rules.activate';
                                                $deactivateRoute = $isAdmin ? 'underwriting.rules.deactivate' : 'officer.underwriting.rules.deactivate';
                                            @endphp

                                                          <a class="inline-flex items-center rounded-md bg-app-hover px-3 py-1.5 text-text-primary ring-1 ring-app-border hover:bg-app-divider"
                                               href="{{ route($editRoute, $rule) }}">Edit</a>

                                            @if($rule->active)
                                                <form method="POST" action="{{ route($deactivateRoute, $rule) }}">
                                                    @csrf
                                                    <x-secondary-button type="submit">Deactivate</x-secondary-button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route($activateRoute, $rule) }}">
                                                    @csrf
                                                    <x-secondary-button type="submit">Activate</x-secondary-button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-text-muted">No rule sets found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $rules->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
