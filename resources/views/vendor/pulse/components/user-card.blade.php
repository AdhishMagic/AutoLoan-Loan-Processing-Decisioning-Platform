@props(['user' => null, 'name' => null, 'extra' => null, 'avatar' => null, 'stats' => null])

<div {{ $attributes->merge(['class' => 'flex items-center justify-between p-3 gap-3 bg-gray-50 dark:bg-gray-800/50 rounded']) }}>
    <div class="flex items-center gap-3 overflow-hidden">
        @if (isset($avatar))
            {{ $avatar }}
        @elseif ($user->avatar ?? false)
            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" loading="lazy" referrerpolicy="no-referrer" class="rounded-full w-8 h-8 object-cover">
        @else
            {{-- Initials-based avatar fallback to avoid third-party tracking --}}
            @php
                $displayName = $user->name ?? $name ?? 'U';
                $words = explode(' ', trim($displayName));
                $initials = count($words) >= 2 
                    ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                    : strtoupper(substr($displayName, 0, 2));
                $colors = [
                    'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-red-500',
                    'bg-purple-500', 'bg-pink-500', 'bg-indigo-500', 'bg-teal-500'
                ];
                $colorIndex = crc32($displayName) % count($colors);
                $bgColor = $colors[$colorIndex];
            @endphp
            <div class="rounded-full w-8 h-8 flex items-center justify-center text-white text-xs font-semibold {{ $bgColor }}">
                {{ $initials }}
            </div>
        @endif

        <div class="overflow-hidden">
            <div class="text-sm text-gray-900 dark:text-gray-100 font-medium truncate" title="{{ $user->name ?? $name }}">
                {{ $user->name ?? $name }}
            </div>

            <div class="text-xs text-gray-500 dark:text-gray-400 truncate" title="{{ $user->extra ?? $extra }}">
                {{ $user->extra ?? $extra }}
            </div>
        </div>
    </div>

    @if (isset($stats))
        <div class="text-xl text-gray-900 dark:text-gray-100 font-bold tabular-nums">
            {{ $stats }}
        </div>
    @endif
</div>
