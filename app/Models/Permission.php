<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission implements PermissionContract
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'guard_name',
        'description',
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

        $permission = static::query()
            ->where('slug', $name)
            ->where('guard_name', $guardName)
            ->first();

        if (! $permission) {
            throw PermissionDoesNotExist::named($name);
        }

        return $permission;
    }

    public static function findOrCreate(string $name, ?string $guardName = null): static
    {
        $guardName ??= app(PermissionRegistrar::class)->getDefaultGuardName(static::class);

        try {
            return static::findByName($name, $guardName);
        } catch (PermissionDoesNotExist) {
            // Create a new record
        }

        return static::query()->create([
            'slug' => $name,
            'name' => Str::headline(str_replace(['*', '.', '_'], ' ', $name)),
            'guard_name' => $guardName,
        ]);
    }

    /**
     * @return BelongsToMany<Role, Permission>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            config('permission.table_names.role_has_permissions')
        );
    }

    /**
     * @return MorphToMany<User>
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'model', config('permission.table_names.model_has_permissions'));
    }
}
