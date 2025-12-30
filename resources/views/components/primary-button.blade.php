<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-brand-accent border border-transparent rounded-md font-semibold text-xs text-text-onDark uppercase tracking-widest hover:bg-brand-accent/90 focus:bg-brand-accent active:bg-brand-accent focus:outline-none focus:ring-2 focus:ring-brand-focus focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
