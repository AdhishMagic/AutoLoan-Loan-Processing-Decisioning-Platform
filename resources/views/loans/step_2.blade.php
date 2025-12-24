@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl">
  <h1 class="text-xl font-semibold mb-4">Loan Application — Step {{ $step ?? 2 }}</h1>

  <form method="POST" action="{{ route('loans.step.store', ['loan' => $loan->id, 'step' => $step]) }}" class="space-y-6">
    @csrf
    @include('loans._form')
  </form>
</div>
@endsection
