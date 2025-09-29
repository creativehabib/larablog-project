<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole implements RoleContract
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'guard_name',
        'summary',
    ];

    protected $attributes = [
        'guard_name' => 'web',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function findByName(string $name, ?string $guardName = null): static
    {
        $guardName ??= app(PermissionRegistrar::class)->getDefaultGuardName(static::class);

        $role = static::query()
            ->where('slug', $name)
            ->where('guard_name', $guardName)
            ->first();

        if (! $role) {
            throw RoleDoesNotExist::named($name);
        }

        return $role;
    }

    public static function findOrCreate(string $name, ?string $guardName = null): static
    {
        $guardName ??= app(PermissionRegistrar::class)->getDefaultGuardName(static::class);

        try {
            return static::findByName($name, $guardName);
        } catch (RoleDoesNotExist) {
            // Continue and create the role
        }

        return static::query()->create([
            'slug' => $name,
            'name' => Str::headline(str_replace(['*', '.', '_'], ' ', $name)),
            'guard_name' => $guardName,
        ]);
    }

    /**
     * @return MorphToMany<User>
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'model', config('permission.table_names.model_has_roles'));
    }

    /**
     * @return BelongsToMany<Permission, Role>
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            config('permission.table_names.role_has_permissions')
        );
    }
}
