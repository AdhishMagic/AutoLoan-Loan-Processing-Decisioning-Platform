<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">API Keys</h2>
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 hover:text-gray-900">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 sm:rounded-lg p-3 text-sm">{{ session('status') }}</div>
            @endif

            @if(!empty($newToken))
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 sm:rounded-lg p-4">
                    <div class="font-medium mb-1">API Key Created</div>
                    <p class="text-sm">Copy your key now. It won't be shown again.</p>
                    <pre class="mt-2 p-3 bg-white border rounded text-xs overflow-x-auto">{{ $newToken }}</pre>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-3">API OTP Verification</h3>
                @if(auth()->user()?->api_otp_verified_at)
                    <div class="text-sm text-green-700">Verified on {{ optional(auth()->user()->api_otp_verified_at)->toDayDateTimeString() }}.</div>
                @else
                    <div class="text-sm text-gray-700 mb-3">You must verify via email OTP before creating or using API keys.</div>
                    <form method="POST" action="{{ route('api-keys.otp.send') }}" class="flex items-end gap-3 mb-3">
                        @csrf
                        <x-primary-button type="submit">Send OTP</x-primary-button>
                    </form>
                    <form method="POST" action="{{ route('api-keys.otp.verify') }}" class="flex items-end gap-3">
                        @csrf
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Enter OTP</label>
                            <input type="text" name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required class="border-gray-300 rounded-md" placeholder="123456" />
                        </div>
                        <x-primary-button type="submit">Verify</x-primary-button>
                    </form>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Create New API Key</h3>
                <form method="POST" action="{{ route('api-keys.store') }}" class="flex items-end gap-3">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-sm text-gray-700 mb-1">Name</label>
                        <input type="text" name="name" required maxlength="64" class="w-full border-gray-300 rounded-md" placeholder="e.g. Mobile App" />
                    </div>
                    <x-primary-button type="submit">Create</x-primary-button>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Existing API Keys</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-gray-600">
                            <tr>
                                <th class="text-left py-2">Name</th>
                                <th class="text-left py-2">Created</th>
                                <th class="text-left py-2">Last Used</th>
                                <th class="text-left py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tokens as $token)
                                <tr class="border-t">
                                    <td class="py-2">{{ $token->name }}</td>
                                    <td class="py-2">{{ optional($token->created_at)->diffForHumans() }}</td>
                                    <td class="py-2">{{ optional($token->last_used_at)->diffForHumans() ?? '—' }}</td>
                                    <td class="py-2">
                                        <form method="POST" action="{{ route('api-keys.destroy', $token->id) }}" onsubmit="return confirm('Revoke this API key?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button type="submit">Revoke</x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-3 text-gray-500">No API keys yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
