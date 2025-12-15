<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Loan Application') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('loans.update', $loan) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Loan Type</label>
                        <input name="loan_type" type="text" value="{{ old('loan_type', $loan->loan_type) }}" class="mt-1 block w-full rounded-md border-gray-300" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Requested Amount</label>
                        <input name="requested_amount" type="number" step="0.01" value="{{ old('requested_amount', $loan->requested_amount) }}" class="mt-1 block w-full rounded-md border-gray-300" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tenure (months)</label>
                        <input name="tenure_months" type="number" value="{{ old('tenure_months', $loan->tenure_months) }}" class="mt-1 block w-full rounded-md border-gray-300" required>
                    </div>

                    <div class="pt-4 flex items-center gap-2">
                        <a href="{{ route('loans.show', $loan) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
