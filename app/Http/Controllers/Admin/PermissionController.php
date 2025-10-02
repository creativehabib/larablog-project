<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:permission.list', only: ['index']),
            new Middleware('permission:permission.edit', only: ['edit', 'update']),
            new Middleware('permission:permission.create', only: ['create', 'store']),
            new Middleware('permission:permission.delete', only: ['destroy']),
        ];
    }
    public function index()
    {
        $permissions = Permission::latest()->paginate(10);
        return view('back.pages.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('back.pages.permissions.create');
    }

    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'group_name' => 'required|string',
        ]);

        // Create Permission
        Permission::create([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        return view('back.pages.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
            'group_name' => 'required|string',
        ]);

        // Update Permission
        $permission->update([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
