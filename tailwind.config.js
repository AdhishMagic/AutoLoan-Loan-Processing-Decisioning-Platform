import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import flowbite from 'flowbite/plugin';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './node_modules/flowbite/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    primary: 'rgb(var(--brand-primary) / <alpha-value>)',
                    secondary: 'rgb(var(--brand-secondary) / <alpha-value>)',
                    accent: 'rgb(var(--brand-accent) / <alpha-value>)',
                    focus: 'rgb(var(--brand-focus) / <alpha-value>)',
                },
                app: {
                    bg: 'rgb(var(--app-bg) / <alpha-value>)',
                    surface: 'rgb(var(--app-surface) / <alpha-value>)',
                    sidebar: 'rgb(var(--app-sidebar) / <alpha-value>)',
                    hover: 'rgb(var(--app-hover) / <alpha-value>)',
                    border: 'rgb(var(--app-border) / <alpha-value>)',
                    divider: 'rgb(var(--app-divider) / <alpha-value>)',
                    disabled: 'rgb(var(--app-disabled) / <alpha-value>)',
                },
                text: {
                    primary: 'rgb(var(--text-primary) / <alpha-value>)',
                    secondary: 'rgb(var(--text-secondary) / <alpha-value>)',
                    muted: 'rgb(var(--text-muted) / <alpha-value>)',
                    onDark: 'rgb(var(--text-onDark) / <alpha-value>)',
                },
                status: {
                    success: 'rgb(var(--status-success) / <alpha-value>)',
                    warning: 'rgb(var(--status-warning) / <alpha-value>)',
                    danger: 'rgb(var(--status-danger) / <alpha-value>)',
                    info: 'rgb(var(--status-info) / <alpha-value>)',
                },
                toggle: {
                    bg: 'rgb(var(--toggle-bg) / <alpha-value>)',
                    hover: 'rgb(var(--toggle-hover) / <alpha-value>)',
                    sun: 'rgb(var(--toggle-sun) / <alpha-value>)',
                    moon: 'rgb(var(--toggle-moon) / <alpha-value>)',
                },
            },
        },
    },

    plugins: [forms, flowbite],
};
