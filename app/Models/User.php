<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Concerns\HasRolesAndPermissions;
use App\Support\Permissions\RoleRegistry;
use App\UserStatus;
use App\UserType;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRolesAndPermissions;

    /**
     * The guard name expected by spatie/laravel-permission.
     */
    protected string $guard_name = 'web';

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

        $type = $this->normalizedRoleTypeAttribute();

        if ($type !== '') {
            return $type;
        }

        return app(RoleRegistry::class)->defaultRole();
    }

    /**
     * Retrieve the role definition from the configuration.
     *
     * @return array{label?: string, summary?: string|null, permissions?: array<int, string>}
     */
    public function roleDefinition(): array
    {
        $registry = app(RoleRegistry::class);
        $role = $this->primaryRole();

        if ($role) {
            $role->loadMissing('permissions');

            return [
                'label' => $role->name,
                'summary' => $role->summary ?? $registry->definition($role->slug)?->summary,
                'permissions' => $role->permissions->pluck('slug')->values()->all(),
            ];
        }

        $key = $this->roleKey();

        if ($definition = $registry->definition($key)) {
            return [
                'label' => $definition->label,
                'summary' => $definition->summary,
                'permissions' => $definition->permissions,
            ];
        }

        return [
            'label' => ucfirst(str_replace('_', ' ', $key)),
            'summary' => null,
            'permissions' => $this->permissionNames(),
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

}
