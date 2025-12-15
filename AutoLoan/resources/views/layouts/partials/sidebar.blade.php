<!-- Sidebar using Flowbite Drawer -->
<aside id="sidebar" class="fixed left-0 top-14 z-40 h-[calc(100vh-3.5rem)] w-64 sm:w-60 -translate-x-full overflow-y-auto border-r bg-white p-3 sm:p-4 transition-transform lg:static lg:translate-x-0">
  <!-- Role-based menu (dummy role selection for UI demo) -->
  <div class="mb-4">
    <label for="role" class="mb-2 block text-xs font-medium text-gray-500">Current Role</label>
    <select id="role" class="w-full rounded-lg border-gray-300 text-sm">
      <option>User (Applicant)</option>
      <option>Loan Officer</option>
      <option>Admin</option>
      <option>Customer Support</option>
    </select>
  </div>

  <div class="space-y-2">
    <!-- Applicant Menu -->
    <div data-role="User (Applicant)" class="space-y-1">
      <a href="#" class="flex items-center rounded-lg px-2.5 sm:px-3 py-2 text-sm hover:bg-gray-100">
        <span>Dashboard</span>
      </a>
      <a href="#" class="flex items-center rounded-lg px-2.5 sm:px-3 py-2 text-sm hover:bg-gray-100">
        <span>Apply for Auto Loan</span>
      </a>
      <a href="#" class="flex items-center rounded-lg px-2.5 sm:px-3 py-2 text-sm hover:bg-gray-100">
        <span>My Loan Applications</span>
      </a>
    </div>

    <!-- Officer Menu -->
    <div data-role="Loan Officer" class="space-y-1 hidden">
      <a href="#" class="flex items-center rounded-lg px-3 py-2 text-sm hover:bg-gray-100">
        <span>Dashboard</span>
      </a>
      <a href="#" class="flex items-center rounded-lg px-3 py-2 text-sm hover:bg-gray-100">
        <span>Loan Applications</span>
      </a>
    </div>

    <!-- Admin Menu -->
    <div data-role="Admin" class="space-y-1 hidden">
      <a href="#" class="flex items-center rounded-lg px-3 py-2 text-sm hover:bg-gray-100">
        <span>Admin Dashboard</span>
      </a>
      <a href="#" class="flex items-center rounded-lg px-3 py-2 text-sm hover:bg-gray-100">
        <span>Manage Users</span>
      </a>
      <a href="#" class="flex items-center rounded-lg px-3 py-2 text-sm hover:bg-gray-100">
        <span>Roles & Permissions</span>
      </a>
      <a href="#" class="flex items-center rounded-lg px-3 py-2 text-sm hover:bg-gray-100">
        <span>Loan Products</span>
      </a>
    </div>

    <!-- Support Menu -->
    <div data-role="Customer Support" class="space-y-1 hidden">
      <a href="#" class="flex items-center rounded-lg px-3 py-2 text-sm hover:bg-gray-100">
        <span>View Loan Applications</span>
      </a>
    </div>
  </div>

  <!-- Minimal JS to toggle role menus -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const roleSelect = document.getElementById('role');
      const sections = document.querySelectorAll('#sidebar [data-role]');
      const update = () => {
        sections.forEach(s => {
          s.classList.toggle('hidden', s.getAttribute('data-role') !== roleSelect.value);
        });
      };
      roleSelect.addEventListener('change', update);
      update();
    });
  </script>
</aside>