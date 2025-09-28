<?php

namespace Tests\Unit;

use App\Models\User;
use App\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_has_access_to_all_permissions(): void
    {
        $user = User::factory()->create([
            'type' => UserType::SuperAdmin,
        ]);

        $this->assertTrue($user->hasPermission('manage_anything'));
    }

    public function test_administrator_can_access_admin_panel(): void
    {
        $user = User::factory()->create([
            'type' => UserType::Administrator,
        ]);

        $this->assertTrue($user->canAccessAdminPanel());
        $this->assertTrue($user->hasPermission('manage_content'));
    }

    public function test_subscriber_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create([
            'type' => UserType::Subscriber,
        ]);

        $this->assertFalse($user->canAccessAdminPanel());
        $this->assertTrue($user->hasPermission('read_and_comment'));
    }
}
