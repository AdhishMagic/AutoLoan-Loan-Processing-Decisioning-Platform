<!-- Top Navbar using Flowbite -->
<nav class="bg-white/95 border-b backdrop-blur supports-[backdrop-filter]:bg-white/70">
  <div class="mx-auto w-full max-w-7xl px-3 sm:px-4">
    <div class="flex h-14 sm:h-16 items-center justify-between">
      <!-- Left: App Name + Mobile menu button -->
      <div class="flex items-center">
        <button data-drawer-target="sidebar" data-drawer-show="sidebar" aria-controls="sidebar" class="mr-2 inline-flex items-center rounded-lg p-2 text-sm text-gray-500 hover:bg-gray-100 focus:outline-none lg:hidden">
          <span class="sr-only">Open sidebar</span>
          <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <a href="#" class="text-base font-semibold text-gray-900">Auto Loan</a>
      </div>

      <!-- Center: Search (optional) -->
      <div class="hidden md:block w-1/3">
        <div class="relative">
          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.414-1.414l4.387 4.387a1 1 0 01-1.414 1.414l-4.387-4.387zM14 8a6 6 0 11-12 0 6 6 0 0112 0z" clip-rule="evenodd" /></svg>
          </div>
          <input type="text" class="block w-full rounded-lg border-gray-300 pl-10 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Search..." />
        </div>
      </div>

      <!-- Right: Notifications + Profile -->
      <div class="flex items-center gap-1 sm:gap-2">
        <button type="button" class="relative inline-flex items-center rounded-lg p-2 text-gray-500 hover:bg-gray-100">
          <span class="sr-only">View notifications</span>
          <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
          <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-medium text-white">3</span>
        </button>

        <button id="profileMenuButton" data-dropdown-toggle="profileMenu" class="flex items-center gap-2 rounded-lg p-2 hover:bg-gray-100">
          <img src="https://api.dicebear.com/9.x/initials/svg?seed=AL" alt="avatar" class="h-8 w-8 rounded-full" />
          <span class="hidden md:inline text-sm font-medium">John Doe</span>
          <svg class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 011.08 1.04l-4.25 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
        </button>
        <!-- Profile dropdown -->
        <div id="profileMenu" class="z-50 hidden w-56 divide-y divide-gray-100 rounded-lg bg-white shadow">
          <div class="px-4 py-3 text-sm">
            <div class="font-medium">John Doe</div>
            <div class="truncate text-gray-500">john.doe@example.com</div>
          </div>
          <ul class="py-2 text-sm" aria-labelledby="profileMenuButton">
            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">Profile</a></li>
            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">Settings</a></li>
            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">Sign out</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</nav>