<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Underwriting Rule
            </h2>
            <a href="{{ route('officer.underwriting.rules.index') }}" class="text-sm text-gray-700 hover:text-gray-900">Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('officer.underwriting.rules.store') }}" class="space-y-4">
                    @csrf

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
                        <x-primary-button type="submit">Create</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
