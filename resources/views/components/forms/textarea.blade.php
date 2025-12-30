@props(['name','label'=>null,'value'=>null,'rows'=>4,'required'=>false])
<div class="space-y-1">
  @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-text-secondary">{{ $label }} @if($required)<span class="text-status-danger">*</span>@endif</label>
  @endif
  <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" @if($required) required @endif
            {{ $attributes->class(['block w-full rounded-md border-app-border shadow-sm focus:border-brand-secondary focus:ring-brand-focus disabled:border-app-disabled disabled:bg-app-bg']) }}>{{ old($name, $value) }}</textarea>
  @error($name)
    <p class="text-sm text-status-danger">{{ $message }}</p>
  @enderror
</div>
