@props(['name','label'=>null,'type'=>'text','value'=>null,'required'=>false])
<div class="space-y-1">
  @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-text-secondary">{{ $label }} @if($required)<span class="text-status-danger">*</span>@endif</label>
  @endif
  <input {{ $attributes->class(['block w-full rounded-md border-app-border shadow-sm focus:border-brand-secondary focus:ring-brand-focus disabled:border-app-disabled disabled:bg-app-bg']) }}
         type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}" @if($required) required @endif>
  @error($name)
    <p class="text-sm text-status-danger">{{ $message }}</p>
  @enderror
  @isset($help)
    <p class="text-xs text-text-muted">{{ $help }}</p>
  @endisset
</div>
