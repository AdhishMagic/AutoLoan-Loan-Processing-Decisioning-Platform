@props(['name','label'=>null,'options'=>[],'value'=>null,'required'=>false])
<div class="space-y-1">
  @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</label>
  @endif
  <select id="{{ $name }}" name="{{ $name }}" @if($required) required @endif
          {{ $attributes->class(['block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500']) }}>
    <option value="">-- Select --</option>
    @foreach($options as $key => $text)
      <option value="{{ $key }}" @selected(old($name, $value) == $key)>{{ $text }}</option>
    @endforeach
  </select>
  @error($name)
    <p class="text-sm text-red-600">{{ $message }}</p>
  @enderror
</div>
