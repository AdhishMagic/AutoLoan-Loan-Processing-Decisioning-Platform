@props(['title' => null, 'actions' => null])
<div {{ $attributes->class(['rounded-lg border bg-white shadow-sm']) }}>
  @if($title || $actions)
    <div class="flex items-center justify-between border-b px-4 py-3">
      @if($title)
        <h3 class="text-sm font-semibold text-gray-900">{{ $title }}</h3>
      @endif
      @if($actions)
        <div class="flex items-center gap-2">{{ $actions }}</div>
      @endif
    </div>
  @endif
  <div class="p-4">
    {{ $slot }}
  </div>
</div>