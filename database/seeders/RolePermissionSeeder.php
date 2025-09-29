<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Default role and permission definitions keyed by role slug.
     *
     * @var array<string, array<string, mixed>>
     */
    protected array $definitions = [
        UserType::SuperAdmin->value => [
            'label' => 'সুপার অ্যাডমিন',
            'permissions' => ['*', 'manage_roles'],
        ],
        UserType::Administrator->value => [
            'label' => 'অ্যাডমিনিস্ট্রেটর',
           'permissions' => [
                'access_admin_panel',
                'manage_content',
                'publish_posts',
                'edit_any_post',
                'create_posts',
                'edit_own_posts',
                'submit_posts',
                'schedule_posts',
            ],
        ],
        UserType::Editor->value => [
            'label' => 'এডিটর',
           'permissions' => [
                'access_admin_panel',
                'publish_posts',
                'edit_any_post',
                'create_posts',
                'edit_own_posts',
                'review_posts',
                'manage_categories',
                'verify_content',
            ],
        ],
        UserType::Author->value => [
            'label' => 'লেখক/রিপোর্টার',
            'permissions' => [
                'access_admin_panel',
                'create_posts',
                'edit_own_posts',
                'delete_own_posts',
                'upload_media',
                'submit_posts',
            ],
        ],
        UserType::Contributor->value => [
            'label' => 'কন্ট্রিবিউটর',
            'permissions' => [
                'access_admin_panel',
                'create_posts',
                'submit_posts',
            ],
        ],
        UserType::Subscriber->value => [
            'label' => 'সাবস্ক্রাইবার',
            'permissions' => [
                'read_and_comment',
            ],
        ],
    ];

    /**
     * Seed the application's roles and permissions.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->definitions as $slug => $definition) {
            $role = Role::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $definition['label'] ?? Str::headline($slug),
                    'guard_name' => $this->guardName(),
                ]
            );

            $permissions = $definition['permissions'] ?? [];

            $permissionModels = collect($permissions)
                ->flatten()
                ->filter(fn ($permission) => is_string($permission) && $permission !== '')
                ->map(function (string $permission) {
                    $label = $permission === '*'
                        ? 'All Permissions'
                        : Str::headline(str_replace(['*', '.'], ' ', $permission));

                    return Permission::query()->updateOrCreate(
                        ['slug' => $permission],
                        [
                            'name' => $label !== '' ? $label : Str::headline($permission),
                            'guard_name' => $this->guardName(),
                        ]
                    );
                })
                ->all();

            $role->syncPermissions($permissionModels);
        }
    }

    protected function guardName(): string
    {
        return config('auth.defaults.guard', 'web');
    }
}
