<?php

namespace App\Models\Concerns;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRolesAndPermissions
{
    /**
     * @return BelongsToMany<Role, self>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * @return BelongsToMany<Permission, self>
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }

    /**
     * Assign one or many roles to the model.
     */
    public function assignRole(string|int|Role|array ...$roles): static
    {
        $storedRoles = $this->resolveRoles($roles);

        if ($storedRoles->isEmpty()) {
            return $this;
        }

        $this->roles()->syncWithoutDetaching($storedRoles->pluck('id')->all());

        if (! $this->primaryRole()) {
            $this->syncPrimaryRoleAttribute($storedRoles->first());
        }

        return $this->load('roles');
    }

    /**
     * Replace any currently assigned roles with the provided set.
     */
    public function syncRoles(string|int|Role|array ...$roles): static
    {
        $storedRoles = $this->resolveRoles($roles);

        $this->roles()->sync($storedRoles->pluck('id')->all());

        $this->syncPrimaryRoleAttribute($storedRoles->first());

        return $this->load('roles');
    }

    /**
     * Remove the provided role from the model.
     */
    public function removeRole(string|int|Role $role): static
    {
        $storedRole = $this->resolveRole($role);

        $this->roles()->detach($storedRole);

        if ($this->primaryRole()?->is($storedRole)) {
            $this->syncPrimaryRoleAttribute($this->roles()->first());
        }

        return $this->load('roles');
    }

    /**
     * Determine if the model has (at least) one of the provided roles.
     */
    public function hasRole(string|int|Role|array ...$roles): bool
    {
        $storedRoles = $this->resolveRoles($roles);

        if ($storedRoles->isEmpty()) {
            return false;
        }

        $this->loadMissing('roles');

        return $storedRoles->contains(fn (Role $role) => $this->roles->contains('id', $role->id));
    }

    /**
     * Determine if the model has any of the provided roles.
     */
    public function hasAnyRole(string|int|Role|array ...$roles): bool
    {
        return $this->hasRole(...$roles);
    }

    /**
     * Determine if the model has all of the provided roles.
     */
    public function hasAllRoles(string|int|Role|array ...$roles): bool
    {
        $storedRoles = $this->resolveRoles($roles);

        if ($storedRoles->isEmpty()) {
            return false;
        }

        $this->loadMissing('roles');

        return $storedRoles->every(fn (Role $role) => $this->roles->contains('id', $role->id));
    }

    /**
     * Grant one or many direct permissions to the model.
     */
    public function givePermissionTo(string|int|Permission|array ...$permissions): static
    {
        $storedPermissions = $this->resolvePermissions($permissions);

        if ($storedPermissions->isEmpty()) {
            return $this;
        }

        $this->permissions()->syncWithoutDetaching($storedPermissions->pluck('id')->all());

        return $this->load('permissions');
    }

    /**
     * Sync direct permissions for the model.
     */
    public function syncPermissions(string|int|Permission|array ...$permissions): static
    {
        $storedPermissions = $this->resolvePermissions($permissions);

        $this->permissions()->sync($storedPermissions->pluck('id')->all());

        return $this->load('permissions');
    }

    /**
     * Revoke the provided direct permission(s) from the model.
     */
    public function revokePermissionTo(string|int|Permission|array ...$permissions): static
    {
        $storedPermissions = $this->resolvePermissions($permissions);

        if ($storedPermissions->isEmpty()) {
            return $this;
        }

        $this->permissions()->detach($storedPermissions->pluck('id')->all());

        return $this->load('permissions');
    }

    /**
     * Determine if the model has the provided permission via roles or direct assignment.
     */
    public function hasPermissionTo(string|Permission $permission): bool
    {
        $permissionModel = $permission instanceof Permission
            ? $permission
            : Permission::query()->where('slug', $permission)->first();

        if (! $permissionModel) {
            if (method_exists($this, 'permissionNames')) {
                /** @var list<string> $names */
                $names = $this->permissionNames();

                if (in_array('*', $names, true) || in_array((string) $permission, $names, true)) {
                    return true;
                }
            }

            $this->loadMissing('permissions', 'roles.permissions');

            return $this->allPermissions()->contains(fn (Permission $perm) => $perm->slug === $permission || $perm->slug === '*');
        }

        return $this->hasPermissionModel($permissionModel);
    }

    /**
     * Determine if the model has the provided permission directly assigned.
     */
    public function hasDirectPermission(string|Permission $permission): bool
    {
        $this->loadMissing('permissions');

        if ($permission instanceof Permission) {
            return $this->permissions->contains('id', $permission->id);
        }

        return $this->permissions->contains('slug', $permission) || $this->permissions->contains('slug', '*');
    }

    /**
     * Retrieve all permissions assigned to the model via roles or directly.
     */
    public function allPermissions(): EloquentCollection
    {
        $this->loadMissing('permissions', 'roles.permissions');

        /** @var EloquentCollection<int, Permission> $direct */
        $direct = $this->permissions;

        /** @var EloquentCollection<int, Permission> $viaRoles */
        $viaRoles = $this->roles->flatMap(fn (Role $role) => $role->permissions)->unique('id');

        return $direct->merge($viaRoles)->unique('id');
    }

    /**
     * Get the primary/most relevant role for the model.
     */
    public function primaryRole(): ?Role
    {
        $this->loadMissing('roles');

        if ($this->roles->isEmpty()) {
            return null;
        }

        $typeAttribute = (string) ($this->getAttribute('type') ?? '');

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
     * Normalize the provided role inputs into stored models.
     */
    protected function resolveRoles(array $roles): Collection
    {
        return collect($roles)
            ->flatten()
            ->filter(fn ($role) => $role !== null && $role !== '')
            ->map(fn ($role) => $this->resolveRole($role))
            ->unique(fn (Role $role) => $role->getKey())
            ->values();
    }

    /**
     * Resolve a single role value into a stored model instance.
     */
    protected function resolveRole(string|int|Role $role): Role
    {
        if ($role instanceof Role) {
            return $role;
        }

        $query = Role::query();

        if (is_numeric($role)) {
            return $query->whereKey((int) $role)->firstOrFail();
        }

        return $query
            ->where('slug', $role)
            ->orWhere('name', $role)
            ->firstOrFail();
    }

    /**
     * Normalize permission inputs into stored models.
     */
    protected function resolvePermissions(array $permissions): Collection
    {
        return collect($permissions)
            ->flatten()
            ->filter(fn ($permission) => $permission !== null && $permission !== '')
            ->map(fn ($permission) => $this->resolvePermission($permission))
            ->unique(fn (Permission $permission) => $permission->getKey())
            ->values();
    }

    /**
     * Resolve a single permission value into a stored model instance.
     */
    protected function resolvePermission(string|int|Permission $permission): Permission
    {
        if ($permission instanceof Permission) {
            return $permission;
        }

        $query = Permission::query();

        if (is_numeric($permission)) {
            return $query->whereKey((int) $permission)->firstOrFail();
        }

        return $query
            ->where('slug', $permission)
            ->orWhere('name', $permission)
            ->firstOrFail();
    }

    /**
     * Persist the primary role representation on the model if applicable.
     */
    protected function syncPrimaryRoleAttribute(?Role $role): void
    {
        if (! $this->isFillable('type') && ! array_key_exists('type', $this->getAttributes())) {
            return;
        }

        $value = $role?->slug;

        if ($value === null) {
            $value = config('roles.default', 'subscriber');
        }

        $this->forceFill(['type' => $value]);
        $this->saveQuietly();
    }

    /**
     * Determine if a permission model is assigned either directly or via roles.
     */
    protected function hasPermissionModel(Permission $permission): bool
    {
        $this->loadMissing('permissions', 'roles.permissions');

        if ($this->permissions->contains('id', $permission->id)) {
            return true;
        }

        if ($this->permissions->contains('slug', '*')) {
            return true;
        }

        if ($permission->slug === '*') {
            return $this->permissions->isNotEmpty() || $this->roles->isNotEmpty();
        }

        return $this->roles->contains(fn (Role $role) => $role->permissions->contains('id', $permission->id) || $role->permissions->contains('slug', '*'));
    }
}
