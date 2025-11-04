<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $permissionsAll = [
            [
                'group_name' => 'Roles',
                'permissions' => [
                    'role.view',
                    'role.create',
                    'role.edit',
                    'role.delete',
                ]
            ],
            [
                'group_name' => 'Permissions',
                'permissions' => [
                    'permission.view',
                    'permission.create',
                    'permission.edit',
                    'permission.delete',
                ]
            ],
            [
                'group_name' => 'Users',
                'permissions' => [
                    'user.view',
                    'user.create',
                    'user.edit',
                    'user.delete',
                ]
            ],
            [
                'group_name' => 'Settings',
                'permissions' => [
                    'setting.view'
                ]
            ],
            [
                'group_name' => 'Categories',
                'permissions' => [
                    'category.view',
                    'category.create',
                    'category.edit',
                    'category.delete',
                ]
            ],
            [
                'group_name' => 'Sub Categories',
                'permissions' => [
                    'subcategory.view',
                    'subcategory.create',
                    'subcategory.edit',
                    'subcategory.delete',
                ]
            ],
            [
                'group_name' => 'Media Library',
                'permissions' => [
                    'media.view',
                    'media.create',
                    'media.edit',
                    'media.delete',
                ]
            ],
            [
                'group_name' => 'Posts',
                'permissions' => [
                    'post.view',
                    'post.create',
                    'post.edit',
                    'post.delete',
                ]
            ],
            [
                'group_name' => 'Polls',
                'permissions' => [
                    'poll.view',
                    'poll.create',
                    'poll.edit',
                    'poll.delete',
                ]
            ],
            [
                'group_name' => 'Menus',
                'permissions' => [
                    'menu.view',
                    'menu.create',
                    'menu.edit',
                    'menu.delete',
                ]
            ]
        ];

        foreach ($permissionsAll as $permGroup) {
            $permissionGroup = $permGroup['group_name'];
            foreach($permGroup['permissions'] as $permissionName) {

                $permission = Permission::firstOrCreate(
                    ['name' => $permissionName, 'guard_name' => 'web'],
                    ['group_name' => $permissionGroup]
                );

                $role->givePermissionTo($permission);
            }
        }

        $user = User::find(1);
        if($user){
            $user->assignRole($role);
        }
    }
}

