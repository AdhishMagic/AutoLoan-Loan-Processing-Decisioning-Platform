@props(['type' => 'default'])
@php
  $type = strtolower((string) $type);
  $classes = [
    'default' => 'bg-gray-100 text-gray-800',
    'success' => 'bg-green-100 text-green-800',
    'warning' => 'bg-yellow-100 text-yellow-800',
    'danger'  => 'bg-red-100 text-red-800',
    'info'    => 'bg-blue-100 text-blue-800',
    'pending' => 'bg-orange-100 text-orange-800',
    'approved'=> 'bg-emerald-100 text-emerald-800',
    'rejected'=> 'bg-rose-100 text-rose-800',
  ][$type] ?? 'bg-gray-100 text-gray-800';
@endphp
<span {{ $attributes->class(['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', $classes]) }}>
  {{ $slot }}
</span>