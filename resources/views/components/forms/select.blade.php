@props(['name','label'=>null,'options'=>[],'value'=>null,'required'=>false])
<div class="space-y-1">
  @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-text-secondary">{{ $label }} @if($required)<span class="text-status-danger">*</span>@endif</label>
  @endif
  <select id="{{ $name }}" name="{{ $name }}" @if($required) required @endif
          {{ $attributes->class(['block w-full rounded-md border-app-border shadow-sm focus:border-brand-secondary focus:ring-brand-focus disabled:border-app-disabled disabled:bg-app-bg']) }}>
    <option value="">-- Select --</option>
    @foreach($options as $key => $text)
      <option value="{{ $key }}" @selected(old($name, $value) == $key)>{{ $text }}</option>
    @endforeach
  </select>
  @error($name)
    <p class="text-sm text-status-danger">{{ $message }}</p>
  @enderror
</div>
