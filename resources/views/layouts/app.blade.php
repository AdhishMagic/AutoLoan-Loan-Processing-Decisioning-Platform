<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'AutoLoan' }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <div class="min-h-screen flex">
        @include('partials.sidebar')
        <div class="flex-1 flex flex-col">
            @include('partials.navbar')
            <main class="p-6">
                @if (session('success'))
                    <div class="mb-4 rounded-md bg-green-50 p-4 text-green-800">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 rounded-md bg-red-50 p-4 text-red-800">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
 
