@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl">
  <h1 class="text-xl font-semibold mb-4">New Loan Application</h1>
  <form method="POST" action="{{ route('loans.store') }}" class="space-y-6">
    @csrf
    @include('loans._form')
  </form>
@endsection

