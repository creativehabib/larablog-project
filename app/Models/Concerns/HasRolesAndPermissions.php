<?php

namespace App\Models\Concerns;

use App\Models\Permission;
use App\Models\Role;
use App\Support\Permissions\RoleRegistry;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;

trait HasRolesAndPermissions
{
    use HasRoles {
        assignRole as spatieAssignRole;
        syncRoles as spatieSyncRoles;
        removeRole as spatieRemoveRole;
        givePermissionTo as spatieGivePermissionTo;
        syncPermissions as spatieSyncPermissions;
        revokePermissionTo as spatieRevokePermissionTo;
    }

    /**
     * Assign the given roles to the model and refresh cached relations.
     */
    public function assignRole(...$roles): static
    {
        $result = $this->spatieAssignRole(...$roles);

        $this->load('roles');
        $this->syncPrimaryRoleAttribute($this->primaryRole());
        $this->forgetPermissionCache();

        return $result;
    }

    /**
     * Sync the given roles on the model and refresh cached relations.
     */
    public function syncRoles(...$roles): static
    {
        $result = $this->spatieSyncRoles(...$roles);

        $this->load('roles');
        $this->syncPrimaryRoleAttribute($this->primaryRole());
        $this->forgetPermissionCache();

        return $result;
    }

    /**
     * Remove the provided role from the model and refresh cached relations.
     */
    public function removeRole($role): static
    {
        $result = $this->spatieRemoveRole($role);

        $this->load('roles');
        $this->syncPrimaryRoleAttribute($this->primaryRole());
        $this->forgetPermissionCache();

        return $result;
    }

    /**
     * Grant the provided permission(s) to the model.
     */
    public function givePermissionTo(...$permissions): static
    {
        $result = $this->spatieGivePermissionTo(...$permissions);

        $this->forgetPermissionCache();

        return $result->load('permissions');
    }

    /**
     * Sync the provided permission(s) on the model.
     */
    public function syncPermissions(...$permissions): static
    {
        $result = $this->spatieSyncPermissions(...$permissions);

        $this->forgetPermissionCache();

        return $result->load('permissions');
    }

    /**
     * Revoke the provided permission(s) from the model.
     */
    public function revokePermissionTo(...$permissions): static
    {
        $result = $this->spatieRevokePermissionTo(...$permissions);

        $this->forgetPermissionCache();

        return $result->load('permissions');
    }

    /**
     * Retrieve all permissions for the model via direct assignment or roles.
     */
    public function allPermissions(): EloquentCollection
    {
        /** @var EloquentCollection<int, Permission> $permissions */
        $permissions = $this->getAllPermissions();

        return $permissions;
    }

    /**
     * Determine if the model has the provided permission slug.
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = $this->permissionNames();

        if (in_array('*', $permissions, true)) {
            return true;
        }

        return in_array($permission, $permissions, true);
    }

    /**
     * Determine if the model has any of the provided permission slugs.
     */
    public function hasAnyPermission(string ...$permissions): bool
    {
        if (empty($permissions)) {
            return $this->hasPermission('access_admin_panel');
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve the permission slugs associated with the model.
     *
     * @return list<string>
     */
    public function permissionNames(): array
    {
        return $this->getAllPermissions()
            ->pluck('slug')
            ->filter(fn ($permission) => is_string($permission) && $permission !== '')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Determine if the model can access the admin panel.
     */
    public function canAccessAdminPanel(): bool
    {
        return $this->hasPermission('access_admin_panel');
    }

    /**
     * Determine the primary role associated with the model.
     */
    public function primaryRole(): ?Role
    {
        $this->loadMissing('roles');

        if ($this->roles->isEmpty()) {
            return null;
        }

        $typeAttribute = $this->normalizedRoleTypeAttribute();

        if ($typeAttribute !== '') {
            $match = $this->roles->firstWhere('slug', $typeAttribute);

            if ($match) {
                return $match;
            }
        }

        return $this->roles
            ->sortByDesc(fn (Role $role) => optional($role->pivot)->created_at)
            ->first();
    }

    /**
     * Persist the current primary role slug on the model when applicable.
     */
    protected function syncPrimaryRoleAttribute(?Role $role): void
    {
        if (! $this->isFillable('type') && ! array_key_exists('type', $this->getAttributes())) {
            return;
        }

        $registry = app(RoleRegistry::class);

        $value = is_string($role?->slug) ? $role->slug : $this->normalizedRoleTypeAttribute();

        if ($value === '') {
            $value = $registry->defaultRole();
        }

        $this->forceFill(['type' => $value]);
        $this->saveQuietly();
    }

    /**
     * Flush the cached permissions maintained by the package registrar.
     */
    protected function forgetPermissionCache(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Resolve the current type attribute into a normalized role slug.
     */
    protected function normalizedRoleTypeAttribute(): string
    {
        $typeAttribute = $this->getAttribute('type');

        if ($typeAttribute instanceof \BackedEnum) {
            return (string) $typeAttribute->value;
        }

        if (is_string($typeAttribute) && $typeAttribute !== '') {
            return $typeAttribute;
        }

        if (is_int($typeAttribute)) {
            return (string) $typeAttribute;
        }

        if (is_object($typeAttribute) && method_exists($typeAttribute, '__toString')) {
            return (string) $typeAttribute;
        }

        return '';
    }
}
