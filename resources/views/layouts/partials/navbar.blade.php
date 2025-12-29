<!-- Top Navbar using Flowbite -->
<nav class="sticky top-0 z-[60] bg-white/95 border-b backdrop-blur supports-[backdrop-filter]:bg-white/70 overflow-visible">
  <div class="mx-auto w-full max-w-7xl px-3 sm:px-4">
    <div class="flex h-14 sm:h-16 items-center justify-between">
      <!-- Left: App Name -->
      <div class="flex items-center shrink-0">
        <a href="{{ route('dashboard') }}" class="text-base font-semibold text-gray-900">Auto Loan</a>
      </div>

      <!-- Center: Main navigation -->
      <div class="hidden lg:flex flex-1 min-w-0 items-center justify-center">
        <div class="flex min-w-0 items-center gap-1 xl:gap-2 overflow-x-auto whitespace-nowrap">
        <a href="{{ route('api-docs') }}" class="rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">API Docs</a>
        @auth
          <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
          @if (Auth::user()->isLoanOfficer() || Auth::user()->isAdmin())
            <a href="{{ route('officer.review') }}" class="rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Officer Review</a>
            @if (Auth::user()->isAdmin())
              <a href="{{ url('/pulse') }}" class="rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Pulse</a>
              <a href="{{ route('underwriting.rules.index') }}" class="rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Underwriting Rules</a>
              <a href="{{ route('admin.loans.index') }}" class="rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">All Applications</a>
            @else
              <a href="{{ route('officer.underwriting.rules.index') }}" class="rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Underwriting Rules</a>
            @endif
          @endif
          @if (Auth::user()->isUser() || Auth::user()->isAdmin())
            <a href="{{ route('loans.create') }}" class="rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Apply for Auto Loan</a>
            <a href="{{ route('loans.index') }}" class="rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">My Loan Applications</a>
          @endif
        @endauth
        </div>
      </div>

      <!-- Mobile: simple inline nav -->
      <div class="lg:hidden flex flex-1 min-w-0 items-center">
        <div class="flex min-w-0 items-center overflow-x-auto whitespace-nowrap">
        <a href="{{ route('api-docs') }}" class="rounded-lg px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-100">API Docs</a>
        @auth
          <a href="{{ route('dashboard') }}" class="rounded-lg px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-100">Home</a>
          @if (Auth::user()->isLoanOfficer() || Auth::user()->isAdmin())
            @if (Auth::user()->isAdmin())
              <a href="{{ url('/pulse') }}" class="ml-1 rounded-lg px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-100">Pulse</a>
              <a href="{{ route('admin.loans.index') }}" class="ml-1 rounded-lg px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-100">All Apps</a>
            @endif
            <a href="{{ route('officer.review') }}" class="ml-1 rounded-lg px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-100">Review</a>
            @if (Auth::user()->isAdmin())
              <a href="{{ route('underwriting.rules.index') }}" class="ml-1 rounded-lg px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-100">Rules</a>
            @else
              <a href="{{ route('officer.underwriting.rules.index') }}" class="ml-1 rounded-lg px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-100">Rules</a>
            @endif
          @endif
          @if (Auth::user()->isUser() || Auth::user()->isAdmin())
            <a href="{{ route('loans.index') }}" class="ml-1 rounded-lg px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-100">Loans</a>
            <a href="{{ route('loans.create') }}" class="ml-1 rounded-lg px-2 py-1.5 text-sm text-white bg-indigo-600 hover:bg-indigo-700">New App</a>
          @endif
        @endauth
        </div>
      </div>

      <!-- Right: Notifications + Profile -->
      <div class="relative z-[60] flex items-center gap-1 sm:gap-2">
        @auth
        @php
          $unreadCount = Auth::user()->unreadNotifications()->count();
          $recentNotifications = Auth::user()->notifications()->latest()->limit(5)->get();
        @endphp

        <button id="notificationMenuButton" data-dropdown-toggle="notificationMenu" data-dropdown-placement="bottom-end" type="button" class="relative inline-flex items-center rounded-lg p-2 text-gray-500 hover:bg-gray-100">
          <span class="sr-only">View notifications</span>
          <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
          @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-medium text-white">{{ $unreadCount }}</span>
          @endif
        </button>

        <div id="notificationMenu" class="fixed right-3 top-14 sm:top-16 z-[80] hidden w-80 divide-y divide-gray-100 rounded-lg bg-white shadow-xl">
          <div class="px-4 py-3 text-sm">
            <div class="font-medium text-gray-900">Notifications</div>
            <div class="text-gray-500">{{ $unreadCount }} unread</div>
          </div>
          <ul class="max-h-96 overflow-auto py-2 text-sm">
            @forelse($recentNotifications as $notification)
              @php
                $title = $notification->data['title'] ?? 'Notification';
                $message = $notification->data['message'] ?? null;
              @endphp
              <li>
                <a href="{{ route('notifications.open', $notification) }}" class="block px-4 py-2 hover:bg-gray-100 {{ $notification->read_at ? 'text-gray-700' : 'font-medium text-gray-900' }}">
                  <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                      <div class="truncate">{{ $title }}</div>
                      @if(is_string($message) && $message !== '')
                        <div class="mt-0.5 line-clamp-2 text-xs text-gray-500">{{ $message }}</div>
                      @endif
                    </div>
                    <div class="shrink-0 text-xs text-gray-400">{{ $notification->created_at?->diffForHumans() }}</div>
                  </div>
                </a>
              </li>
            @empty
              <li class="px-4 py-4 text-sm text-gray-500">No notifications yet.</li>
            @endforelse
          </ul>
        </div>

        <button id="profileMenuButton" data-dropdown-toggle="profileMenu" data-dropdown-placement="bottom-end" class="flex items-center gap-2 rounded-lg p-2 hover:bg-gray-100">
          <img src="https://api.dicebear.com/9.x/initials/svg?seed={{ urlencode(Auth::user()->name ?? 'U') }}" alt="avatar" class="h-8 w-8 rounded-full" />
          <span class="hidden md:inline text-sm font-medium">{{ Auth::user()->name }}</span>
          <svg class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 011.08 1.04l-4.25 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
        </button>
        <!-- Profile dropdown -->
        <div id="profileMenu" class="fixed right-3 top-14 sm:top-16 z-[80] hidden w-56 divide-y divide-gray-100 rounded-lg bg-white shadow-xl">
          <div class="px-4 py-3 text-sm">
            <div class="font-medium">{{ Auth::user()->name }}</div>
            <div class="truncate text-gray-500">{{ Auth::user()->email }}</div>
          </div>
          <ul class="py-2 text-sm" aria-labelledby="profileMenuButton">
            <li><a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-100">Profile</a></li>
            <li><a href="{{ route('api-keys.index') }}" class="block px-4 py-2 hover:bg-gray-100">API Keys</a></li>
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Sign out</button>
              </form>
            </li>
          </ul>
        </div>
        @else
          <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">Log in</a>
        @endauth
      </div>
    </div>
  </div>
</nav>