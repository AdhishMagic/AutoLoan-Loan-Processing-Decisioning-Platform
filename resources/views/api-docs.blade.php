<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">API Docs</h2>
            <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Dashboard login</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <div class="text-sm text-gray-700">
                        <h3 class="text-base font-semibold text-gray-900">Introduction</h3>
                        <p class="mt-1">
                            The AutoLoan API is a REST-based, JSON-driven interface for integrating AutoLoan loan processing into partner systems,
                            mobile applications, and internal automations.
                        </p>
                        <p class="mt-2">
                            This page is read-only and intended to help you understand how access is secured and where to find the full reference.
                        </p>
                    </div>

                    <div class="mt-6 text-sm">
                        <h3 class="text-base font-semibold text-gray-900">Authentication</h3>
                        <p class="mt-1 text-gray-700">
                            Requests are authenticated using a Bearer token (API key). The API key identifies your client and protects access.
                        </p>
                        <p class="mt-3 text-gray-600">Required request header</p>
                        <pre class="mt-1 rounded border border-gray-200 bg-gray-50 p-3 text-xs overflow-auto">Authorization: Bearer &lt;API_KEY&gt;</pre>
                    </div>

                    <div class="mt-6 text-sm">
                        <h3 class="text-base font-semibold text-gray-900">How to get an API key</h3>
                        <p class="mt-1 text-gray-700">API keys are issued from the AutoLoan dashboard.</p>
                        <ul class="mt-3 list-disc pl-5 text-gray-700 space-y-1">
                            <li>Log in to the AutoLoan dashboard</li>
                            <li>Navigate to Profile → API Keys</li>
                            <li>Complete OTP verification</li>
                            <li>Create a new API key</li>
                            <li>Copy and store the key securely (it is shown only once)</li>
                        </ul>
                        <p class="mt-3 text-gray-700">
                            Do not share API keys. If a key is exposed, revoke it immediately and generate a new one.
                        </p>
                    </div>

                    <div class="mt-6 text-sm">
                        <h3 class="text-base font-semibold text-gray-900">Rate limits & security</h3>
                        <ul class="mt-3 list-disc pl-5 text-gray-700 space-y-1">
                            <li>Rate limit: 60 requests per minute per API key</li>
                            <li>Users can access only their own loan records</li>
                            <li>Officers can access only assigned loans</li>
                            <li>API keys can be revoked at any time</li>
                            <li>Access is logged and monitored</li>
                        </ul>
                    </div>

                    <div class="mt-6 text-sm">
                        <h3 class="text-base font-semibold text-gray-900">Error behavior</h3>
                        <p class="mt-1 text-gray-700">Common HTTP responses you may encounter:</p>
                        <ul class="mt-3 list-disc pl-5 text-gray-700 space-y-1">
                            <li><span class="font-medium">401</span> — Missing or invalid API key</li>
                            <li><span class="font-medium">403</span> — OTP verification required or access denied</li>
                            <li><span class="font-medium">429</span> — Too many requests (rate limit exceeded)</li>
                        </ul>
                    </div>

                    <div class="mt-6 text-sm">
                        <h3 class="text-base font-semibold text-gray-900">Detailed API Reference</h3>
                        <p class="mt-1 text-gray-700">
                            Full request/response details, parameters, and examples are available in the Postman-hosted documentation.
                        </p>
                        <div class="mt-3">
                            @if(is_string($postmanDocsUrl) && trim($postmanDocsUrl) !== '' && !str_contains($postmanDocsUrl, 'XXXXX'))
                                <a
                                    href="{{ $postmanDocsUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                                >
                                    View full API reference
                                </a>
                            @else
                                <span class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500">
                                    View full API reference (link provided separately)
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
