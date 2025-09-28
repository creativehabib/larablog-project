<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriesTable extends Component
{
    use WithPagination;

    #[Url(as: 'search', except: '')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function deleteCategory(int $categoryId): void
    {
        $category = Category::withCount('subCategories')->findOrFail($categoryId);

        if ($category->image_path && Storage::disk('public')->exists($category->image_path)) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();

        session()->flash('success', 'Category deleted successfully.');
    }

    public function render()
    {
        $categories = Category::withCount('subCategories')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $searchTerm = "%{$this->search}%";

                    $q->where('name', 'like', $searchTerm)
                        ->orWhere('slug', 'like', $searchTerm)
                        ->orWhere('description', 'like', $searchTerm);
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.admin.categories-table', [
            'categories' => $categories,
        ]);
    }
}
