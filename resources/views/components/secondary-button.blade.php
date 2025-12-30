<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-app-surface border border-app-border rounded-md font-semibold text-xs text-text-secondary uppercase tracking-widest shadow-sm hover:bg-app-hover focus:outline-none focus:ring-2 focus:ring-brand-focus focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
