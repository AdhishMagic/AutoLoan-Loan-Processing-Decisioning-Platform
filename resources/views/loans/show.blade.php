@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-xl font-semibold">Application Details</h1>
    <div class="flex items-center gap-2">
      @can('update', $loan)
        @php($nextStep = (($loan->stage_order ?? 0) + 1))
        @if($loan->isDraft())
          <a href="{{ route('loans.step.show', ['loan' => $loan->id, 'step' => $nextStep]) }}" class="inline-flex items-center rounded-md bg-brand-accent px-3 py-2 text-text-onDark hover:bg-brand-accent/90">Continue</a>
        @else
          <form method="POST" action="{{ route('loans.destroy', $loan) }}" onsubmit="return confirm('Delete this application?')">
            @csrf
            @method('DELETE')
            <button class="inline-flex items-center rounded-md bg-status-danger px-3 py-2 text-text-onDark hover:bg-status-danger/90">Delete</button>
          </form>
        @endif
      @endcan
    </div>
  </div>

  <div class="bg-app-surface rounded-lg shadow-sm ring-1 ring-app-border p-6 grid grid-cols-2 gap-4">
    <div>
      <div class="text-xs text-text-muted">Loan Type</div>
      <div class="font-medium">{{ $loan->loan_type }}</div>
    </div>
    <div>
      <div class="text-xs text-text-muted">Requested Amount</div>
      <div class="font-medium">{{ number_format($loan->requested_amount, 2) }}</div>
    </div>
    <div>
      <div class="text-xs text-text-muted">Tenure</div>
      <div class="font-medium">{{ $loan->tenure_months }} months</div>
    </div>
    <div>
      <div class="text-xs text-text-muted">Status</div>
      <div class="font-medium">{{ ucfirst($loan->status) }}</div>
    </div>
  </div>

  <div class="bg-app-surface rounded-lg shadow-sm ring-1 ring-app-border p-6">
    <div class="text-sm font-semibold mb-2">Timeline</div>
    <div class="space-y-3">
      @foreach($loan->statusHistory as $item)
        <div class="flex items-start gap-3">
          <div class="mt-1 size-2 rounded-full bg-brand-secondary"></div>
          <div class="text-sm w-full">
            <div class="flex items-center justify-between">
              <div class="font-medium">{{ $item->action_title }}</div>
              <div class="text-xs text-text-muted">{{ $item->action_timestamp?->format('Y-m-d H:i') }}</div>
            </div>
            @if($item->action_description)
              <div class="text-text-secondary">{{ $item->action_description }}</div>
            @endif
            <div class="mt-1 flex items-center gap-2 text-xs">
              @if($item->actor_role)
                <span class="inline-flex items-center rounded-full bg-app-bg px-2 py-0.5 text-text-secondary ring-1 ring-app-border">
                  {{ $item->actor_role }}
                </span>
              @endif
              @if($item->stage)
                <span class="inline-flex items-center rounded-full bg-app-bg px-2 py-0.5 text-text-secondary ring-1 ring-app-border">
                  Stage: {{ $item->stage }}
                </span>
              @endif
              @if($item->current_status)
                @php($cs = strtolower((string) $item->current_status))
                <span class="inline-flex items-center rounded-full px-2 py-0.5
                  {{ $cs === 'approved' ? 'bg-status-success/10 text-status-success' : ($cs === 'rejected' ? 'bg-status-danger/10 text-status-danger' : ($cs === 'manual_review' ? 'bg-status-warning/10 text-status-warning' : 'bg-status-info/10 text-status-info')) }}">
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

