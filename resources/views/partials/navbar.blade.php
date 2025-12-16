<header class="border-b bg-white">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
    <div class="font-semibold">AutoLoan</div>
    <nav class="flex items-center gap-4 text-sm">
      <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
      @role('user')
        <a href="{{ route('loans.index') }}" class="hover:text-blue-600">My Loans</a>
      @endrole
      @anyrole('manager','admin')
        <a href="{{ route('officer.review') }}" class="hover:text-blue-600">Review</a>
      @endanyrole
      @role('admin')
        <a href="{{ route('admin.users.index') }}" class="hover:text-blue-600">Admin</a>
      @endrole
    </nav>
  </div>
</header>
