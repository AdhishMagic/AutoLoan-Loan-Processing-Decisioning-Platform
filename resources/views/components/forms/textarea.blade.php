@props(['name','label'=>null,'value'=>null,'rows'=>4,'required'=>false])
<div class="space-y-1">
  @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</label>
  @endif
  <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" @if($required) required @endif
            {{ $attributes->class(['block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500']) }}>{{ old($name, $value) }}</textarea>
  @error($name)
    <p class="text-sm text-red-600">{{ $message }}</p>
  @enderror
</div>
