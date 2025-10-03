<?php

namespace App\Livewire\Admin;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;

class SitemapManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $status = 'all';

    public function updatedSearch(): void
    {
        $this->search = trim($this->search);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function toggleIndexable(int $postId): void
    {
        $post = Post::findOrFail($postId);
        $post->update(['is_indexable' => ! $post->is_indexable]);

        $totalPosts = Post::count();
        $indexablePosts = Post::where('is_indexable', true)->count();

        $this->dispatch('showToastr', type: 'success', message: 'Sitemap visibility updated.');
        $this->dispatch('sitemapCoverageUpdated', coverage: [
            'indexable' => $indexablePosts,
            'non_indexable' => $totalPosts - $indexablePosts,
            'total' => $totalPosts,
            'coverage' => $totalPosts > 0 ? round(($indexablePosts / $totalPosts) * 100, 1) : 0,
        ]);
    }

    public function render()
    {
        $query = Post::query()
            ->with(['category', 'author'])
            ->orderByDesc('updated_at');

        if ($this->search !== '') {
            $term = '%'.$this->search.'%';
            $query->where(function ($builder) use ($term) {
                $builder->where('title', 'like', $term)
                    ->orWhere('slug', 'like', $term)
                    ->orWhereHas('category', function ($categoryQuery) use ($term) {
                        $categoryQuery->where('name', 'like', $term);
                    })
                    ->orWhereHas('author', function ($authorQuery) use ($term) {
                        $authorQuery->where('name', 'like', $term);
                    });
            });
        }

        if ($this->status === 'indexable') {
            $query->where('is_indexable', true);
        } elseif ($this->status === 'no-index') {
            $query->where('is_indexable', false);
        }

        return view('livewire.admin.sitemap-manager', [
            'posts' => $query->paginate(6),
        ]);
    }
}
