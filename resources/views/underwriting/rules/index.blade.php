<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Underwriting Rules
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-sm text-gray-600">Manage JSON rule sets used for automated underwriting.</div>
                    @php
                        $isAdmin = auth()->check() && auth()->user()->role?->name === 'admin';
                    @endphp
                    @if(!$isAdmin)
                        <a class="inline-flex items-center rounded-md bg-gray-200 px-3 py-2 text-gray-800 hover:bg-gray-300"
                           href="{{ route('officer.underwriting.rules.create') }}">New Rule</a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-gray-600">
                            <tr>
                                <th class="py-2 pr-4">ID</th>
                                <th class="py-2 pr-4">Name</th>
                                <th class="py-2 pr-4">Active</th>
                                <th class="py-2 pr-4">Updated</th>
                                <th class="py-2 pr-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($rules as $rule)
                                <tr>
                                    <td class="py-2 pr-4 font-medium">{{ $rule->id }}</td>
                                    <td class="py-2 pr-4">{{ $rule->name }}</td>
                                    <td class="py-2 pr-4">
                                        @if($rule->active)
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">Active</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800">Inactive</span>
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

                                            <a class="inline-flex items-center rounded-md bg-gray-200 px-3 py-1.5 text-gray-800 hover:bg-gray-300"
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
                                    <td colspan="5" class="py-4 text-gray-500">No rule sets found.</td>
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
