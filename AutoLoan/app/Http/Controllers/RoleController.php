<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('admin.roles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        // Stub; implement validation/business logic later
        return redirect()->route('admin.roles.index');
    }

    public function show(Role $role): View
    {
        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        // Stub; implement validation/business logic later
        return redirect()->route('admin.roles.show', $role);
    }

    public function destroy(Role $role): RedirectResponse
    {
        // Stub; implement deletion logic later
        return redirect()->route('admin.roles.index');
    }
}
