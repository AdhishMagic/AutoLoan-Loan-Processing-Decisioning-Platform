@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl">
  <h1 class="text-xl font-semibold mb-4">Loan Application — Documents & Submit (Step {{ $step ?? 8 }})</h1>

  <form method="POST" action="{{ route('loans.step.store', ['loan' => $loan->id, 'step' => $step]) }}" class="space-y-6" enctype="multipart/form-data">
    @csrf
    @include('loans._form')

    {{-- Documents area (if controller provided document vars, show a helpful list) --}}
    @isset($documentTypes)
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-semibold mb-3">Required Documents</h3>
        <ul class="list-disc pl-6 text-sm">
          @foreach($requiredDocumentTypes as $dt)
            <li>{{ $documentTypes[$dt] ?? $dt }}</li>
          @endforeach
        </ul>
      </div>
    @endisset
  </form>
</div>
@endsection
