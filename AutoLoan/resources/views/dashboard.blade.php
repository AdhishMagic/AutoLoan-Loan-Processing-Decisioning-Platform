<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>

            @if(Auth::user()?->isAdmin())
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Recent Users</h3>
                            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">New User</a>
                        </div>
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                    <tr>
                                        <th class="px-4 py-3">Name</th>
                                        <th class="px-4 py-3">Email</th>
                                        <th class="px-4 py-3">Role</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($recentUsers = \App\Models\User::with('role')->latest()->limit(5)->get())
                                    @forelse($recentUsers as $u)
                                        <tr class="border-b bg-white">
                                            <td class="px-4 py-3 font-medium">{{ $u->name }}</td>
                                            <td class="px-4 py-3">{{ $u->email }}</td>
                                            <td class="px-4 py-3">{{ $u->role->name ?? '—' }}</td>
                                            <td class="px-4 py-3">{{ $u->status }}</td>
                                            <td class="px-4 py-3">
                                                <a href="{{ route('admin.users.show', $u) }}" class="text-indigo-600 hover:underline">View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">No users found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:underline">View all users</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
