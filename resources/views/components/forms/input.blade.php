@props(['name','label'=>null,'type'=>'text','value'=>null,'required'=>false])
<div class="space-y-1">
  @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</label>
  @endif
  <input {{ $attributes->class(['block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500']) }}
         type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}" @if($required) required @endif>
  @error($name)
    <p class="text-sm text-red-600">{{ $message }}</p>
  @enderror
  @isset($help)
    <p class="text-xs text-gray-500">{{ $help }}</p>
  @endisset
</div>
