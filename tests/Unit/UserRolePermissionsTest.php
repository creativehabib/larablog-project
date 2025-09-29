<?php

namespace Tests\Unit;

use App\Models\User;
use App\UserType;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_has_access_to_all_permissions(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(UserType::SuperAdmin->value);

        $this->assertTrue($user->hasPermission('manage_anything'));
    }

    public function test_administrator_can_access_admin_panel(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(UserType::Administrator->value);

        $this->assertTrue($user->canAccessAdminPanel());
        $this->assertTrue($user->hasPermission('manage_content'));
    }

    public function test_only_super_admin_can_manage_users(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $administrator = User::factory()->create();
        $administrator->assignRole(UserType::Administrator->value);

        $this->assertFalse($administrator->hasPermission('manage_users'));

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserType::SuperAdmin->value);

        $this->assertTrue($superAdmin->hasPermission('manage_users'));
    }

    public function test_subscriber_cannot_access_admin_panel(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(UserType::Subscriber->value);

        $this->assertFalse($user->canAccessAdminPanel());
        $this->assertTrue($user->hasPermission('read_and_comment'));
    }
}
