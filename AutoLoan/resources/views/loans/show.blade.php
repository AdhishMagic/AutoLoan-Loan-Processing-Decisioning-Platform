<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Application #') }}{{ $loan->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <div class="text-xs text-gray-500">Type</div>
                        <div class="text-sm font-medium">{{ $loan->loan_type }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Amount</div>
                        <div class="text-sm font-medium">${{ number_format($loan->requested_amount, 2) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Tenure</div>
                        <div class="text-sm font-medium">{{ $loan->tenure_months }} months</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Status</div>
                        <div class="text-sm font-medium">{{ ucfirst($loan->status) }}</div>
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-2">
                    @can('update', $loan)
                        <a href="{{ route('loans.edit', $loan) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Edit</a>
                        <form method="POST" action="{{ route('loans.destroy', $loan) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-red-600 ring-1 ring-red-300 hover:bg-red-50" onclick="return confirm('Delete this application?')">Delete</button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
