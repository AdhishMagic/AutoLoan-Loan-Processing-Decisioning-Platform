<aside class="hidden md:block w-60 bg-white border-r">
  <div class="p-4 text-sm text-gray-500 uppercase">Menu</div>
  <ul class="px-2 space-y-1">
    <li><a href="{{ route('dashboard') }}" class="block rounded px-3 py-2 hover:bg-gray-100">Home</a></li>
    @role('user')
      <li><a href="{{ route('loans.index') }}" class="block rounded px-3 py-2 hover:bg-gray-100">My Applications</a></li>
      <li><a href="{{ route('loans.create') }}" class="block rounded px-3 py-2 hover:bg-gray-100">New Application</a></li>
    @endrole
    @anyrole('manager','admin')
      <li><a href="{{ route('officer.review') }}" class="block rounded px-3 py-2 hover:bg-gray-100">Officer Review</a></li>
    @endanyrole
    @role('admin')
      <li><a href="{{ route('admin.users.index') }}" class="block rounded px-3 py-2 hover:bg-gray-100">Users</a></li>
      <li><a href="{{ route('admin.roles.index') }}" class="block rounded px-3 py-2 hover:bg-gray-100">Roles</a></li>
    @endrole
  </ul>
</aside>
