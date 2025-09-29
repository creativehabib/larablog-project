<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\UserType;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->superAdmin = User::factory()->create([
            'type' => UserType::SuperAdmin,
        ]);

        $this->superAdmin->assignRole(UserType::SuperAdmin->value);
    }

    public function test_super_admin_can_view_role_index(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.roles.index'));

        $response->assertOk();
        $response->assertSee('Roles & Permissions');
    }

    public function test_super_admin_can_create_role_with_permissions(): void
    {
        $payload = [
            'name' => 'Copy Editor',
            'summary' => 'Ensures copy quality and adherence to style guides.',
            'permissions' => ['manage_content'],
            'new_permissions' => "approve_comments|Approve Comments\nfeature_posts",
        ];

        $response = $this->actingAs($this->superAdmin)->post(route('admin.roles.store'), $payload);

        $response->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('roles', [
            'slug' => 'copy_editor',
            'name' => 'Copy Editor',
        ]);

        $this->assertDatabaseHas('permissions', ['slug' => 'approve_comments', 'name' => 'Approve Comments']);
        $this->assertDatabaseHas('permissions', ['slug' => 'feature_posts']);

        $role = Role::query()->where('slug', 'copy_editor')->first();
        $this->assertNotNull($role);

        $this->assertTrue($role->permissions->pluck('slug')->contains('manage_content'));
        $this->assertTrue($role->permissions->pluck('slug')->contains('approve_comments'));
        $this->assertTrue($role->permissions->pluck('slug')->contains('feature_posts'));
    }

    public function test_super_admin_can_update_role_and_permissions(): void
    {
        $role = Role::query()->create([
            'name' => 'News Reviewer',
            'slug' => 'news_reviewer',
            'summary' => 'Reviews news content before publishing.',
        ]);

        $manageContent = Permission::query()->where('slug', 'manage_content')->firstOrFail();
        $role->permissions()->sync([$manageContent->id]);

        $payload = [
            'name' => 'Senior Editor',
            'slug' => '',
            'summary' => 'Oversees editorial standards across the newsroom.',
            'permissions' => ['publish_posts'],
            'new_permissions' => '',
        ];

        $response = $this->actingAs($this->superAdmin)->put(route('admin.roles.update', $role), $payload);

        $response->assertRedirect(route('admin.roles.index'));

        $role->refresh();

        $this->assertSame('senior_editor', $role->slug);
        $this->assertSame('Senior Editor', $role->name);
        $this->assertSame('Oversees editorial standards across the newsroom.', $role->summary);
        $this->assertTrue($role->permissions->pluck('slug')->contains('publish_posts'));
        $this->assertFalse($role->permissions->pluck('slug')->contains('manage_content'));
    }

    public function test_super_admin_cannot_delete_super_admin_role(): void
    {
        $role = Role::query()->where('slug', UserType::SuperAdmin->value)->firstOrFail();

        $response = $this->actingAs($this->superAdmin)->delete(route('admin.roles.destroy', $role));

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('fail');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'slug' => UserType::SuperAdmin->value,
        ]);
    }
}
