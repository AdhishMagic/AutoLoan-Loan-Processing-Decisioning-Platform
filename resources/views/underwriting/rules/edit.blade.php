<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Underwriting Rule
            </h2>
            @php
                $isAdmin = auth()->check() && auth()->user()->role?->name === 'admin';
                $indexRoute = $isAdmin ? 'underwriting.rules.index' : 'officer.underwriting.rules.index';
                $updateRoute = $isAdmin ? 'underwriting.rules.update' : 'officer.underwriting.rules.update';
                $testRoute = $isAdmin ? 'underwriting.rules.test' : 'officer.underwriting.rules.test';
            @endphp
            <a href="{{ route($indexRoute) }}" class="text-sm text-gray-700 hover:text-gray-900">Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route($updateRoute, $rule) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                    <div>
                        <x-input-label for="name" value="Name" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $rule->name) }}" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="active" name="active" type="checkbox" value="1" {{ old('active', $rule->active) ? 'checked' : '' }}>
                        <label for="active" class="text-sm text-gray-700">Active</label>
                    </div>

                    <div>
                        <x-input-label for="rules_json" value="Rules JSON" />
                        <textarea id="rules_json" name="rules_json" rows="18" class="mt-1 block w-full rounded-md border-gray-300 font-mono text-xs">{{ old('rules_json', $json) }}</textarea>
                        <x-input-error :messages="$errors->get('rules_json')" class="mt-2" />
                    </div>

                        <div class="flex items-center gap-3">
                            <x-primary-button type="submit">Save</x-primary-button>
                        </div>
                </form>

                <hr class="my-6">

                <form method="POST" action="{{ route($testRoute, $rule) }}" class="space-y-3">
                    @csrf
                    <div>
                        <x-input-label for="loan_application_id" value="Test Against Loan Application (UUID)" />
                        <x-text-input id="loan_application_id" name="loan_application_id" type="text" class="mt-1 block w-full" placeholder="e.g. 2f2b..." required />
                        <x-input-error :messages="$errors->get('loan_application_id')" class="mt-2" />
                    </div>
                    <x-secondary-button type="submit">Run Test</x-secondary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
