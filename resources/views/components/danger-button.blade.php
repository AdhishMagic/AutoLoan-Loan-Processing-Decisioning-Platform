<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-status-danger border border-transparent rounded-md font-semibold text-xs text-text-onDark uppercase tracking-widest hover:bg-status-danger/90 active:bg-status-danger focus:outline-none focus:ring-2 focus:ring-brand-focus focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
