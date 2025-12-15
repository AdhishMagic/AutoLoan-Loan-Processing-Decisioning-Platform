@props(['variant' => 'primary', 'size' => 'md', 'icon' => null, 'type' => 'button'])
@php
  $variants = [
    'primary' => 'bg-indigo-600 text-white hover:bg-indigo-700',
    'secondary' => 'bg-gray-100 text-gray-900 hover:bg-gray-200',
    'danger' => 'bg-red-600 text-white hover:bg-red-700',
    'success' => 'bg-emerald-600 text-white hover:bg-emerald-700',
    'link' => 'text-indigo-600 hover:text-indigo-700',
  ];
  $sizes = [
    'sm' => 'text-xs px-2 py-1',
    'md' => 'text-sm px-3 py-2',
    'lg' => 'text-sm px-4 py-2',
  ];
@endphp
<button type="{{ $type }}" {{ $attributes->class(['inline-flex items-center gap-2 rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2', $variants[$variant] ?? $variants['primary'], $sizes[$size] ?? $sizes['md']]) }}>
  @if($icon)
    <span class="inline-block">{!! $icon !!}</span>
  @endif
  {{ $slot }}
</button>