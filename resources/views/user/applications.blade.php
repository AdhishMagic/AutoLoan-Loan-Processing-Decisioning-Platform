<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('My Loan Applications') }}
      </h2>
  </x-slot>

  <div class="py-6">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="mb-6 flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">Track status of your auto loan applications.</p>
          </div>
          <a href="#" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">New Application</a>
        </div>

    <x-card>
      <div class="mb-4 flex flex-wrap items-center gap-2">
        <label class="text-sm text-gray-600">Filter:</label>
        <select class="rounded-lg border-gray-300 text-sm">
          <option>All</option>
          <option>Pending</option>
          <option>Approved</option>
          <option>Rejected</option>
        </select>
        <input type="text" placeholder="Search by ID" class="w-48 rounded-lg border-gray-300 text-sm" />
      </div>

      <div class="relative overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr>
              <th class="px-4 py-3">Application ID</th>
              <th class="px-4 py-3">Vehicle</th>
              <th class="px-4 py-3">Loan Amount</th>
              <th class="px-4 py-3">Submitted</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach([
              ['id' => 'AL-2025-0008', 'vehicle' => 'Toyota Camry 2022', 'amount' => '$18,500', 'date' => 'Dec 12, 2025', 'status' => 'Pending'],
              ['id' => 'AL-2025-0007', 'vehicle' => 'Honda Civic 2021', 'amount' => '$15,000', 'date' => 'Dec 10, 2025', 'status' => 'Approved'],
              ['id' => 'AL-2025-0006', 'vehicle' => 'Ford F-150 2020', 'amount' => '$27,000', 'date' => 'Dec 08, 2025', 'status' => 'Rejected'],
              ['id' => 'AL-2025-0005', 'vehicle' => 'Tesla Model 3 2023', 'amount' => '$35,000', 'date' => 'Dec 01, 2025', 'status' => 'Pending'],
            ] as $row)
              <tr class="border-b bg-white">
                <td class="px-4 py-3 font-medium">{{ $row['id'] }}</td>
                <td class="px-4 py-3">{{ $row['vehicle'] }}</td>
                <td class="px-4 py-3">{{ $row['amount'] }}</td>
                <td class="px-4 py-3">{{ $row['date'] }}</td>
                <td class="px-4 py-3">
                  @php
                    $type = strtolower($row['status']);
                    $map = ['pending' => 'pending', 'approved' => 'approved', 'rejected' => 'rejected'];
                  @endphp
                  <x-badge :type="$map[$type]">{{ $row['status'] }}</x-badge>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <x-button variant="secondary" size="sm">View</x-button>
                    <x-button variant="primary" size="sm">Timeline</x-button>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-4 text-xs text-gray-500">Sample data shown; wire to backend when ready.</div>
    </x-card>
      </div>
    </div>
  </div>
</x-app-layout>