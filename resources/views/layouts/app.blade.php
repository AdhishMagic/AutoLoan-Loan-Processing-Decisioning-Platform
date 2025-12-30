<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <title>{{ $title ?? 'AutoLoan' }}</title>
    <script>
        (function () {
            try {
                const stored = localStorage.getItem('theme');
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const dark = stored ? stored === 'dark' : prefersDark;
                document.documentElement.classList.toggle('dark', dark);
            } catch (e) {
                // no-op
            }
        })();
    </script>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-app-bg text-text-primary">
    <div class="min-h-screen flex flex-col">
        @include('layouts.partials.navbar')
        <main class="p-6">
                @if (session('success'))
                    <div class="mb-4 rounded-md bg-status-success/10 p-4 text-status-success ring-1 ring-app-border">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 rounded-md bg-status-danger/10 p-4 text-status-danger ring-1 ring-app-border">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
        </main>
    </div>
</body>
</html>
 
