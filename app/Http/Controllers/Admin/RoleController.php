<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('permissions')->with('permissions')->get();
        return view('back.pages.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        $groupedPermissions = $permissions->groupBy('group_name')->sortKeys();
        return view('back.pages.roles.create', compact('groupedPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array'
        ]);

        $role = Role::create(['name' => $request->name]);
        if ($request->has('permissions')) {
            $role->givePermissionTo($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }


    public function edit(Role $role)
    {
        // FIX: Group permissions just like in the create method for consistency.
        $permissions = Permission::all();
        $groupedPermissions = $permissions->groupBy('group_name')->sortKeys();
        return view('back.pages.roles.edit', compact('role', 'groupedPermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array'
        ]);

        $role->update(['name' => $request->name]);

        // Use an empty array if no permissions are sent
        $permissions = $request->input('permissions', []);
        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
