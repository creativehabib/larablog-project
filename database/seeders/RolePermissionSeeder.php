<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Support\Permissions\RoleDefinition;
use App\Support\Permissions\RoleRegistry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function __construct(protected RoleRegistry $roles)
    {
    }

    /**
     * Seed the application's roles and permissions from the registry.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = $this->roles->guard();

        $this->syncRoles($guard);

        if ($this->roles->shouldPruneMissing()) {
            $this->pruneMissingRoles($this->roles->slugs(), $guard);
            $this->pruneMissingPermissions($this->roles->declaredPermissions(), $guard);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function syncRoles(string $guard): void
    {
        $this->roles->definitions()->each(function (RoleDefinition $definition) use ($guard): void {
            $role = Role::query()->updateOrCreate(
                ['slug' => $definition->slug],
                [
                    'name' => $definition->label,
                    'summary' => $definition->summary,
                    'guard_name' => $guard,
                ]
            );

            $permissions = collect($definition->permissions)
                ->map(fn (string $permission) => $this->persistPermission($permission, $guard))
                ->filter()
                ->values()
                ->all();

            $role->syncPermissions($permissions);
        });
    }

    protected function persistPermission(string $slug, string $guard): Permission
    {
        return Permission::query()->updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $this->permissionLabel($slug),
                'guard_name' => $guard,
            ]
        );
    }

    protected function permissionLabel(string $slug): string
    {
        if ($slug === '*') {
            return 'All Permissions';
        }

        $label = Str::headline(str_replace(['*', '.', '_'], ' ', $slug));

        return $label !== '' ? $label : $slug;
    }

    protected function pruneMissingRoles(array $expectedSlugs, string $guard): void
    {
        Role::query()
            ->where('guard_name', $guard)
            ->whereNotIn('slug', $expectedSlugs)
            ->get()
            ->each(function (Role $role): void {
                $role->permissions()->detach();
                $role->delete();
            });
    }

    protected function pruneMissingPermissions(array $expectedPermissions, string $guard): void
    {
        Permission::query()
            ->where('guard_name', $guard)
            ->whereNotIn('slug', $expectedPermissions)
            ->get()
            ->each(function (Permission $permission): void {
                $permission->roles()->detach();
                $permission->delete();
            });
    }
}
