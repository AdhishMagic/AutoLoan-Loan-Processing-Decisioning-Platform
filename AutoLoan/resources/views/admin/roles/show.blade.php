<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Role Details') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-3">
                <div class="text-sm"><span class="text-gray-500">Name:</span> <span class="font-medium">{{ $role->name }}</span></div>
                <div class="text-sm"><span class="text-gray-500">Description:</span> <span class="font-medium">{{ $role->description }}</span></div>
                <div class="pt-4">
                    <a href="{{ route('admin.roles.edit', $role) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Edit</a>
                    <a href="{{ route('admin.roles.index') }}" class="ms-2 inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50">Back</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
