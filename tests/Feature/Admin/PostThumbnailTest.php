<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\PostForm;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class PostThumbnailTest extends TestCase
{
    use RefreshDatabase;

    public function test_thumbnail_selected_from_media_library_is_saved_with_normalized_path(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $category = Category::create([
            'name' => 'News',
            'slug' => Str::slug('News'),
        ]);

        $mediaPath = 'media-library/example.jpg';
        Storage::disk('public')->put($mediaPath, 'fake image contents');
        $url = Storage::disk('public')->url($mediaPath);

        Livewire::actingAs($user)
            ->test(PostForm::class)
            ->set('title', 'Library Thumbnail Post')
            ->set('description', 'Example content for the post.')
            ->set('category_id', (string) $category->id)
            ->set('cover_image', $url)
            ->set('cover_image_path', $url)
            ->call('setCoverImageFromLibrary', $url, $url)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.posts.index'));

        $this->assertDatabaseHas('posts', [
            'title' => 'Library Thumbnail Post',
            'thumbnail_path' => $mediaPath,
        ]);

        $post = Post::first();
        $this->assertSame($mediaPath, $post->thumbnail_path);
    }
}
