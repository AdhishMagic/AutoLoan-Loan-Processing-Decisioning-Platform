<!-- Sidebar using Flowbite Drawer -->
<aside id="sidebar" class="fixed left-0 top-14 z-40 h-[calc(100vh-3.5rem)] w-64 sm:w-60 -translate-x-full overflow-y-auto border-r bg-white p-3 sm:p-4 transition-transform lg:static lg:translate-x-0">
  <div class="space-y-2">
    @auth
      <!-- Applicant (User) Menu -->
      @role('user')
      <div class="space-y-1">
        <a href="{{ route('dashboard') }}" class="flex items-center rounded-lg px-2.5 sm:px-3 py-2 text-sm hover:bg-gray-100">Dashboard</a>
        <a href="{{ route('loans.create') }}" class="flex items-center rounded-lg px-2.5 sm:px-3 py-2 text-sm hover:bg-gray-100">Apply for Auto Loan</a>
        <a href="{{ route('loans.index') }}" class="flex items-center rounded-lg px-2.5 sm:px-3 py-2 text-sm hover:bg-gray-100">My Loan Applications</a>
      </div>
      @endrole

      <!-- Loan Officer (manager) -->
      @role('manager')
      <div class="space-y-1">
        <a href="{{ route('dashboard') }}" class="flex items-center rounded-lg px-2.5 sm:px-3 py-2 text-sm hover:bg-gray-100">Dashboard</a>
        <a href="{{ route('officer.review') }}" class="flex items-center rounded-lg px-2.5 sm:px-3 py-2 text-sm hover:bg-gray-100">Loan Applications</a>
      </div>
      @endrole

      <!-- Admin -->
      @role('admin')
      <div class="space-y-1">
        <a href="{{ route('dashboard') }}" class="flex items-center rounded-lg px-2.5 sm:px-3 py-2 text-sm hover:bg-gray-100">Admin Dashboard</a>
        <a href="{{ route('admin.users.index') }}" class="flex items-center rounded-lg px-2.5 sm:px-3 py-2 text-sm hover:bg-gray-100">Manage Users</a>
        <a href="{{ route('admin.roles.index') }}" class="flex items-center rounded-lg px-2.5 sm:px-3 py-2 text-sm hover:bg-gray-100">Roles & Permissions</a>
      </div>
      @endrole

      <!-- Customer Support -->
      @role('customer_service')
      <div class="space-y-1">
        <a href="{{ route('support.loans.index') }}" class="flex items-center rounded-lg px-2.5 sm:px-3 py-2 text-sm hover:bg-gray-100">View Loan Applications</a>
      </div>
      @endrole
    @endauth
  </div>
</aside>