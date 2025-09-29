<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index(): View
    {
        $roles = Role::query()
            ->with(['permissions' => fn ($query) => $query->orderBy('name')])
            ->withCount(['permissions', 'users'])
            ->orderBy('name')
            ->get();

        return view('back.pages.roles.index', [
            'pageTitle' => 'Roles & Permissions',
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
        return view('back.pages.roles.create', [
            'pageTitle' => 'Create Role',
            'permissions' => $this->availablePermissions(),
        ]);
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'slug')],
            'new_permissions' => ['nullable', 'string'],
        ]);

        $slug = $this->makeUniqueSlug($validated['slug'] ?? $validated['name']);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'summary' => $validated['summary'] ?? null,
        ]);

        $permissionSlugs = $this->mergeAdditionalPermissions(
            $validated['permissions'] ?? [],
            $validated['new_permissions'] ?? ''
        );

        $this->syncRolePermissions($role, $permissionSlugs);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('Role created successfully.'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role): View
    {
        $role->load('permissions');

        return view('back.pages.roles.edit', [
            'pageTitle' => 'Edit Role',
            'role' => $role,
            'permissions' => $this->availablePermissions(),
        ]);
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'slug')],
            'new_permissions' => ['nullable', 'string'],
        ]);

        $slug = $this->makeUniqueSlug($validated['slug'] ?? $validated['name'], $role->id);

        $role->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'summary' => $validated['summary'] ?? null,
        ]);

        $permissionSlugs = $this->mergeAdditionalPermissions(
            $validated['permissions'] ?? [],
            $validated['new_permissions'] ?? ''
        );

        $this->syncRolePermissions($role, $permissionSlugs);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('Role updated successfully.'));
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        if ($role->slug === 'superadmin') {
            return redirect()
                ->route('admin.roles.index')
                ->with('fail', __('The superadmin role cannot be deleted.'));
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('Role deleted successfully.'));
    }

    /**
     * Retrieve the available permissions ordered for display.
     */
    protected function availablePermissions(): Collection
    {
        return Permission::query()
            ->orderByRaw("CASE WHEN slug = '*' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();
    }

    /**
     * Generate a unique slug for the role.
     */
    protected function makeUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = $this->normalizeSlug($value);

        if ($baseSlug === '') {
            $baseSlug = Str::random(8);
        }

        $slug = $baseSlug;
        $counter = 1;

        while (
            Role::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$counter++;
        }

        return $slug;
    }

    /**
     * Normalize the provided slug string.
     */
    protected function normalizeSlug(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        $value = Str::lower($value);
        $value = str_replace([' ', '__'], '_', $value);
        $value = preg_replace('/[^a-z0-9_\-\.\*]/', '', $value) ?? '';
        $value = preg_replace('/_+/', '_', $value) ?? '';
        $value = trim($value, '_-');

        return $value;
    }

    /**
     * Combine selected permissions with any additional entries.
     *
     * @param  array<int, string>  $selected
     * @return array<int, string>
     */
    protected function mergeAdditionalPermissions(array $selected, string $additional): array
    {
        $selected = collect($selected)
            ->filter(fn ($permission) => is_string($permission) && $permission !== '')
            ->values();

        $extra = collect(preg_split('/\r\n|\r|\n/', $additional) ?: [])
            ->map(fn ($line) => trim($line))
            ->filter()
            ->map(function (string $line) {
                [$slug, $label] = array_pad(array_map('trim', explode('|', $line, 2)), 2, null);

                $slug = $this->normalizeSlug($slug ?? '');

                if ($slug === '') {
                    return null;
                }

                $name = $label ?? '';

                if ($name === '') {
                    $name = Str::headline(str_replace(['*', '.', '_'], ' ', $slug));
                }

                $permission = Permission::query()->updateOrCreate(
                    ['slug' => $slug],
                    ['name' => $name]
                );

                return $permission->slug;
            })
            ->filter()
            ->values();

        return $selected->merge($extra)->unique()->values()->all();
    }

    /**
     * Sync the provided permissions with the role.
     *
     * @param  array<int, string>  $permissionSlugs
     */
    protected function syncRolePermissions(Role $role, array $permissionSlugs): void
    {
        $permissionIds = Permission::query()
            ->whereIn('slug', $permissionSlugs)
            ->pluck('id')
            ->all();

        $role->permissions()->sync($permissionIds);
    }
}
