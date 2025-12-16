@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">My Loan Applications</h1>
    <a href="{{ route('loans.create') }}" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-white hover:bg-blue-700">New Application</a>
  </div>

  <div class="bg-white shadow rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tenure</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
          <th class="px-4 py-2"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 bg-white">
        @forelse($loans as $loan)
          <tr>
            <td class="px-4 py-2 text-sm text-gray-700">{{ $loan->application_number ?? $loan->id }}</td>
            <td class="px-4 py-2 text-sm text-gray-700">{{ $loan->loan_type }}</td>
            <td class="px-4 py-2 text-sm text-gray-700">{{ number_format($loan->requested_amount, 2) }}</td>
            <td class="px-4 py-2 text-sm text-gray-700">{{ $loan->tenure_months }} months</td>
            <td class="px-4 py-2 text-sm"><span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs">{{ ucfirst($loan->status) }}</span></td>
            <td class="px-4 py-2 text-right text-sm">
              <a href="{{ route('loans.show', $loan) }}" class="text-blue-600 hover:underline">View</a>
            </td>
          </tr>
        @empty
          <tr><td class="px-4 py-6 text-center text-sm text-gray-500" colspan="6">No applications yet</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $loans->links() }}</div>
</div>
@endsection

