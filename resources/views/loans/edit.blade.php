@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl">
  <h1 class="text-xl font-semibold mb-4">Edit Loan Application</h1>
  <form method="POST" action="{{ route('loans.update', $loan) }}" class="space-y-6">
    @csrf
    @method('PUT')
    @include('loans._form', ['loan' => $loan])
  </form>
@endsection

