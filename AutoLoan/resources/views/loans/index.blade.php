<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Loan Applications') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-4 flex items-center justify-between">
                    <div class="text-sm text-gray-600">Track and manage your applications.</div>
                    <a href="{{ route('loans.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">New Application</a>
                </div>

                <div class="relative overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Amount</th>
                                <th class="px-4 py-3">Tenure</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                                <tr class="border-b bg-white">
                                    <td class="px-4 py-3 font-medium">#{{ $loan->id }}</td>
                                    <td class="px-4 py-3">{{ $loan->loan_type }}</td>
                                    <td class="px-4 py-3">${{ number_format($loan->requested_amount, 2) }}</td>
                                    <td class="px-4 py-3">{{ $loan->tenure_months }} months</td>
                                    <td class="px-4 py-3">{{ ucfirst($loan->status) }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('loans.show', $loan) }}" class="text-indigo-600 hover:underline">View</a>
                                            @can('update', $loan)
                                                <a href="{{ route('loans.edit', $loan) }}" class="text-gray-700 hover:underline">Edit</a>
                                                <form method="POST" action="{{ route('loans.destroy', $loan) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Delete this application?')">Delete</button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">No applications yet.</td>
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
