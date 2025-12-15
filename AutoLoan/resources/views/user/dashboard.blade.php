@extends('layouts.app')

@section('title', 'Applicant Dashboard')

@section('content')
  <div class="mx-auto max-w-7xl">
    <div class="mb-6">
      <h1 class="text-xl font-semibold">Welcome back, John</h1>
      <p class="text-sm text-gray-500">Here is an overview of your auto loans.</p>
    </div>

    <!-- Summary cards -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <x-card title="Total Loans">
        <div class="flex items-end justify-between">
          <div>
            <div class="text-2xl font-semibold">8</div>
            <div class="text-xs text-gray-500">Across all applications</div>
          </div>
          <div class="rounded-lg bg-indigo-50 p-3">
            <svg class="h-6 w-6 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12c0 6-4.5 9-9 9s-9-3-9-9 4.5-9 9-9 9 3 9 9z"/></svg>
          </div>
        </div>
      </x-card>

      <x-card title="Approved">
        <div class="flex items-end justify-between">
          <div>
            <div class="text-2xl font-semibold">3</div>
            <div class="text-xs text-gray-500">Approved applications</div>
          </div>
          <x-badge type="approved">Approved</x-badge>
        </div>
      </x-card>

      <x-card title="Pending">
        <div class="flex items-end justify-between">
          <div>
            <div class="text-2xl font-semibold">4</div>
            <div class="text-xs text-gray-500">Under review</div>
          </div>
          <x-badge type="pending">Pending</x-badge>
        </div>
      </x-card>

      <x-card title="Rejected">
        <div class="flex items-end justify-between">
          <div>
            <div class="text-2xl font-semibold">1</div>
            <div class="text-xs text-gray-500">Rejected applications</div>
          </div>
          <x-badge type="rejected">Rejected</x-badge>
        </div>
      </x-card>
    </div>

    <!-- Quick actions -->
    <div class="mt-6 grid gap-4 lg:grid-cols-3">
      <x-card title="Quick Actions">
        <div class="flex flex-wrap gap-2">
          <x-button variant="primary">Apply for Auto Loan</x-button>
          <x-button variant="secondary">View Applications</x-button>
          <x-button variant="link">Upload Documents</x-button>
        </div>
      </x-card>

      <x-card title="Recent Activity" class="lg:col-span-2">
        <ul class="space-y-3 text-sm">
          <li class="flex items-start justify-between">
            <div>
              <div class="font-medium">Application #AL-2025-0008</div>
              <div class="text-gray-500">Submitted vehicle documents</div>
            </div>
            <x-badge type="pending">Under Review</x-badge>
          </li>
          <li class="flex items-start justify-between">
            <div>
              <div class="font-medium">Application #AL-2025-0007</div>
              <div class="text-gray-500">Approved by Loan Officer</div>
            </div>
            <x-badge type="approved">Approved</x-badge>
          </li>
          <li class="flex items-start justify-between">
            <div>
              <div class="font-medium">Application #AL-2025-0006</div>
              <div class="text-gray-500">Rejected due to credit score</div>
            </div>
            <x-badge type="rejected">Rejected</x-badge>
          </li>
        </ul>
      </x-card>
    </div>
  </div>
@endsection