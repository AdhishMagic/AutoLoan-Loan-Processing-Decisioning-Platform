@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-xl font-semibold">Application Details</h1>
    <div class="flex items-center gap-2">
      @can('update', $loan)
        @php($nextStep = (($loan->stage_order ?? 0) + 1))
        @if($loan->isDraft())
          <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => $nextStep]) }}" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-white hover:bg-blue-700">Continue</a>
        @else
          <a href="{{ route('loans.edit', $loan) }}" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-white hover:bg-blue-700">Edit</a>
        @endif
        <form method="POST" action="{{ route('loans.destroy', $loan) }}" onsubmit="return confirm('Delete this application?')">
          @csrf @method('DELETE')
          <button class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-white hover:bg-red-700">Delete</button>
        </form>
      @endcan
    </div>
  </div>

  <div class="bg-white rounded-lg shadow p-6 grid grid-cols-2 gap-4">
    <div>
      <div class="text-xs text-gray-500">Loan Type</div>
      <div class="font-medium">{{ $loan->loan_type }}</div>
    </div>
    <div>
      <div class="text-xs text-gray-500">Requested Amount</div>
      <div class="font-medium">{{ number_format($loan->requested_amount, 2) }}</div>
    </div>
    <div>
      <div class="text-xs text-gray-500">Tenure</div>
      <div class="font-medium">{{ $loan->tenure_months }} months</div>
    </div>
    <div>
      <div class="text-xs text-gray-500">Status</div>
      <div class="font-medium">{{ ucfirst($loan->status) }}</div>
    </div>
  </div>

  <div class="bg-white rounded-lg shadow p-6">
    <div class="text-sm font-semibold mb-2">Timeline</div>
    <div class="space-y-3">
      @foreach($loan->statusHistory as $item)
        <div class="flex items-start gap-3">
          <div class="mt-1 size-2 rounded-full bg-gray-400"></div>
          <div class="text-sm w-full">
            <div class="flex items-center justify-between">
              <div class="font-medium">{{ $item->action_title }}</div>
              <div class="text-xs text-gray-500">{{ $item->action_timestamp?->format('Y-m-d H:i') }}</div>
            </div>
            @if($item->action_description)
              <div class="text-gray-600">{{ $item->action_description }}</div>
            @endif
            <div class="mt-1 flex items-center gap-2 text-xs">
              @if($item->actor_role)
                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-gray-700">
                  {{ $item->actor_role }}
                </span>
              @endif
              @if($item->stage)
                <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-blue-700">
                  Stage: {{ $item->stage }}
                </span>
              @endif
              @if($item->current_status)
                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-emerald-700">
                  Status: {{ strtoupper($item->current_status) }}
                </span>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>
@endsection

