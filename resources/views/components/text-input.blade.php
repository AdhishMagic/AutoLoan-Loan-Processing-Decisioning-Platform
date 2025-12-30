@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-app-border focus:border-brand-secondary focus:ring-brand-focus rounded-md shadow-sm disabled:border-app-disabled disabled:bg-app-bg']) }}>
