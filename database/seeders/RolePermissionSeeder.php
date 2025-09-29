<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed the application's roles and permissions.
     */
    public function run(): void
    {
        $definitions = config('roles', []);

        foreach ($definitions as $slug => $definition) {
            if (! is_array($definition)) {
                continue;
            }

            $role = Role::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $definition['label'] ?? Str::headline($slug),
                    'summary' => $definition['summary'] ?? null,
                ]
            );

            $permissions = $definition['permissions'] ?? [];

            $permissionIds = collect($permissions)
                ->flatten()
                ->filter(fn ($permission) => is_string($permission) && $permission !== '')
                ->map(function (string $permission) {
                    $label = $permission === '*'
                        ? 'All Permissions'
                        : Str::headline(str_replace(['*', '.'], ' ', $permission));

                    return Permission::query()->updateOrCreate(
                        ['slug' => $permission],
                        ['name' => $label !== '' ? $label : Str::headline($permission)]
                    );
                })
                ->pluck('id')
                ->all();

            $role->permissions()->sync($permissionIds);
        }
    }
}
