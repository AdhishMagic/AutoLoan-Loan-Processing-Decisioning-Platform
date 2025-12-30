import './bootstrap';

import Alpine from 'alpinejs';
import 'flowbite';

window.Alpine = Alpine;

Alpine.start();

function getInitialDarkMode() {
	try {
		const stored = localStorage.getItem('theme');
		if (stored === 'dark') return true;
		if (stored === 'light') return false;
	} catch (e) {
		// ignore
	}

	return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
}

function setDarkMode(isDark) {
	const html = document.documentElement;
	html.classList.add('theme-transition');
	html.classList.toggle('dark', isDark);

	try {
		localStorage.setItem('theme', isDark ? 'dark' : 'light');
	} catch (e) {
		// ignore
	}

	window.setTimeout(() => html.classList.remove('theme-transition'), 300);

	const toggleButton = document.querySelector('[data-theme-toggle]');
	if (toggleButton) {
		const sun = toggleButton.querySelector('[data-theme-icon="sun"]');
		const moon = toggleButton.querySelector('[data-theme-icon="moon"]');
		if (sun) sun.classList.toggle('hidden', isDark);
		if (moon) moon.classList.toggle('hidden', !isDark);
	}

	window.dispatchEvent(new CustomEvent('theme-changed', { detail: { isDark } }));
}

window.addEventListener('DOMContentLoaded', () => {
	const toggleButton = document.querySelector('[data-theme-toggle]');

	// Ensure icons match the already-bootstrapped <html> class.
	setDarkMode(document.documentElement.classList.contains('dark'));

	if (!toggleButton) return;

	toggleButton.addEventListener('click', () => {
		setDarkMode(!document.documentElement.classList.contains('dark'));
	});
});
