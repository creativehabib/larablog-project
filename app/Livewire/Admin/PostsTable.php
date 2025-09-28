<?php

namespace App\Livewire\Admin;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class PostsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->search = trim($this->search);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function toggleFeatured(int $postId): void
    {
        $post = Post::findOrFail($postId);
        $post->update(['is_featured' => ! $post->is_featured]);

        $this->dispatch('showToastr', type: 'success', message: 'Post featured status updated.');
    }

    public function toggleComments(int $postId): void
    {
        $post = Post::findOrFail($postId);
        $post->update(['allow_comments' => ! $post->allow_comments]);

        $this->dispatch('showToastr', type: 'success', message: 'Comment setting updated.');
    }

    public function toggleIndexable(int $postId): void
    {
        $post = Post::findOrFail($postId);
        $post->update(['is_indexable' => ! $post->is_indexable]);

        $this->dispatch('showToastr', type: 'success', message: 'Indexing preference updated.');
    }

    public function deletePost(int $postId): void
    {
        $post = Post::findOrFail($postId);

        if ($post->thumbnail_path) {
            Storage::disk('public')->delete($post->thumbnail_path);
        }

        $post->delete();

        $this->dispatch('showToastr', type: 'success', message: 'Post removed successfully.');

        $this->resetPage();
    }

    public function render()
    {
        $posts = Post::with(['category', 'subCategory'])
            ->when($this->search !== '', function ($query) {
                $searchTerm = '%'.$this->search.'%';

                $query->where(function ($nested) use ($searchTerm) {
                    $nested->where('title', 'like', $searchTerm)
                        ->orWhere('slug', 'like', $searchTerm)
                        ->orWhere('meta_title', 'like', $searchTerm)
                        ->orWhereHas('category', function ($categoryQuery) use ($searchTerm) {
                            $categoryQuery->where('name', 'like', $searchTerm);
                        })
                        ->orWhereHas('subCategory', function ($subCategoryQuery) use ($searchTerm) {
                            $subCategoryQuery->where('name', 'like', $searchTerm);
                        });
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.admin.posts-table', [
            'posts' => $posts,
        ]);
    }
}
