<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Underwriting Test Result
            </h2>
            @php
                $isAdmin = auth()->check() && auth()->user()->role?->name === 'admin';
                $editRoute = $isAdmin ? 'underwriting.rules.edit' : 'officer.underwriting.rules.edit';
            @endphp
            <a href="{{ route($editRoute, $rule) }}" class="text-sm text-indigo-600 hover:text-indigo-900">Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-sm text-gray-500">Rule Set</div>
                <div class="font-semibold">{{ $rule->name }} (ID: {{ $rule->id }})</div>
                <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">Loan</div>
                        <div class="font-medium">{{ $loan->application_number ?? $loan->id }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Decision</div>
                        <div class="font-medium">{{ $result->decision }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Score</div>
                        <div class="font-medium">{{ $result->score }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-2">Reasons</h3>
                @if(empty($result->reasons))
                    <div class="text-sm text-gray-500">—</div>
                @else
                    <pre class="text-xs whitespace-pre-wrap">{{ json_encode($result->reasons, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-2">Facts Snapshot</h3>
                <pre class="text-xs whitespace-pre-wrap">{{ json_encode($facts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-2">Trace</h3>
                <pre class="text-xs whitespace-pre-wrap">{{ json_encode($result->trace, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    </div>
</x-app-layout>
