<?php

namespace App\Livewire\Admin;

use App\Models\SubCategory;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SubCategoriesTable extends Component
{
    use WithPagination;

    #[Url(as: 'search', except: '')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function deleteSubCategory(int $subCategoryId): void
    {
        $subCategory = SubCategory::with('category')->findOrFail($subCategoryId);

        if ($subCategory->image_path && Storage::disk('public')->exists($subCategory->image_path)) {
            Storage::disk('public')->delete($subCategory->image_path);
        }

        $subCategory->delete();

        session()->flash('success', 'Sub category deleted successfully.');
    }

    public function render()
    {
        $subCategories = SubCategory::with('category')
            ->when($this->search, function ($query) {
                $searchTerm = "%{$this->search}%";

                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                        ->orWhere('slug', 'like', $searchTerm)
                        ->orWhere('description', 'like', $searchTerm)
                        ->orWhereHas('category', function ($categoryQuery) use ($searchTerm) {
                            $categoryQuery->where('name', 'like', $searchTerm);
                        });
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.admin.sub-categories-table', [
            'subCategories' => $subCategories,
        ]);
    }
}
