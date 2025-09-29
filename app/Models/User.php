<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Concerns\HasRolesAndPermissions;
use App\UserStatus;
use App\UserType;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRolesAndPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'avatar',
        'bio',
        'website',
        'type',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
            'type' => UserType::class,
        ];
    }

    public function getAvatarAttribute($value)
    {
        return $value ? asset('/storage/' . $value) : asset('/demo-user.jpg');
    }

    public function social_links(): HasOne
    {
        return $this->hasOne(UserSocialLink::class);
    }

    /**
     * Get the current role key for the user.
     */
    public function roleKey(): string
    {
        $primaryRole = $this->primaryRole();

        if ($primaryRole) {
            return $primaryRole->slug;
        }

        if ($this->type instanceof UserType) {
            return $this->type->value;
        }

        if (is_string($this->type) && $this->type !== '') {
            return $this->type;
        }

        return UserType::Subscriber->value;
    }

    /**
     * Retrieve the role definition from the configuration.
     *
     * @return array{label?: string, summary?: string|null, permissions?: array<int, string>}
     */
    public function roleDefinition(): array
    {
        $role = $this->primaryRole();

        if ($role) {
            $role->loadMissing('permissions');

            return [
                'label' => $role->name,
                'summary' => $role->summary,
                'permissions' => $role->permissions->pluck('slug')->values()->all(),
            ];
        }

        $roles = config('roles', []);
        $key = $this->roleKey();

        if (array_key_exists($key, $roles)) {
            return $roles[$key];
        }

        return $roles[UserType::Subscriber->value] ?? [
            'label' => ucfirst(str_replace('_', ' ', $key)),
            'summary' => null,
            'permissions' => [],
        ];
    }

    /**
     * Get the translated label for the user's role.
     */
    public function roleLabel(): string
    {
        $definition = $this->roleDefinition();

        return $definition['label'] ?? ucfirst(str_replace('_', ' ', $this->roleKey()));
    }

    /**
     * Get the summary/description for the user's role.
     */
    public function roleSummary(): ?string
    {
        $definition = $this->roleDefinition();

        return $definition['summary'] ?? null;
    }

    /**
     * Get the current status key for the user.
     */
    public function statusKey(): string
    {
        if ($this->status instanceof UserStatus) {
            return $this->status->value;
        }

        if (is_string($this->status) && $this->status !== '') {
            return $this->status;
        }

        return UserStatus::Pending->value;
    }

    /**
     * Get the human readable label for the user's status.
     */
    public function statusLabel(): string
    {
        return ucfirst(str_replace('_', ' ', $this->statusKey()));
    }

    /**
     * Get the permissions granted to the user's role.
     *
     * @return list<string>
     */
    /**
     * Get the permissions granted to the user via roles or direct assignment.
     *
     * @return list<string>
     */
    public function permissionNames(): array
    {
        $permissions = $this->allPermissions()->pluck('slug')->unique()->values()->all();

        if ($permissions === []) {
            $definition = $this->roleDefinition();

            return $definition['permissions'] ?? [];
        }

        return $permissions;
    }

    /**
     * Determine if the user has the provided permission.
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
     * Determine if the user has any of the provided permissions.
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
     * Determine if the user can access the admin panel.
     */
    public function canAccessAdminPanel(): bool
    {
        return $this->hasPermission('access_admin_panel');
    }
}
