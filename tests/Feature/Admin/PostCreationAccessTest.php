<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\UserType;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PostCreationAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function contributor_can_access_the_post_creation_form(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create([
            'type' => UserType::Contributor,
        ]);

        $user->assignRole(UserType::Contributor->value);

        $response = $this->actingAs($user)->get(route('admin.posts.create'));

        $response->assertOk();
    }

    /** @test */
    public function submit_post_permission_is_treated_as_create_permission(): void
    {
        Route::middleware('web')->get('/testing/create-permission', fn () => 'ok')
            ->middleware('permission:create_posts');

        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create([
            'type' => UserType::Contributor,
        ]);

        $user->assignRole(UserType::Contributor->value);

        $response = $this->actingAs($user)->get('/testing/create-permission');

        $response->assertOk();
    }
}
