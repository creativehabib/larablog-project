<?php

namespace Tests\Feature;

use App\Models\User;
use App\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMenuVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_cannot_see_management_menus(): void
    {
        $user = User::factory()->create([
            'type' => UserType::Author,
        ]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertDontSee(route('admin.categories.index'), false);
        $response->assertDontSee('Categories', false);
        $response->assertDontSee(route('admin.settings'), false);
        $response->assertDontSee('User Management', false);
        $response->assertSee(route('admin.posts.index'), false);
    }

    public function test_administrator_can_see_all_management_menus(): void
    {
        $user = User::factory()->create([
            'type' => UserType::Administrator,
        ]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee(route('admin.categories.index'), false);
        $response->assertSee('Categories', false);
        $response->assertSee(route('admin.users.index'), false);
        $response->assertSee('User Management', false);
    }

    public function test_author_cannot_access_user_management_page(): void
    {
        $user = User::factory()->create([
            'type' => UserType::Author,
        ]);

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertForbidden();
    }

    public function test_administrator_can_access_user_management_page(): void
    {
        $user = User::factory()->create([
            'type' => UserType::Administrator,
        ]);

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('User Management', false);
    }

    public function test_author_can_access_post_creation_page(): void
    {
        $user = User::factory()->create([
            'type' => UserType::Author,
        ]);

        $response = $this->actingAs($user)->get(route('admin.posts.create'));

        $response->assertOk();
        $response->assertSee('Create Post', false);
    }
}
