<?php

namespace App\Http\Controllers;

use App\Models\PostCategory;
use App\Models\PostSubcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PostSubcategoryController extends Controller
{
    public function index(): View
    {
        $subcategories = PostSubcategory::with('category')
            ->orderBy('name')
            ->paginate(15);

        return view('back.pages.post-subcategories.index', compact('subcategories'));
    }

    public function create(): View
    {
        $categories = PostCategory::orderBy('name')->get();

        return view('back.pages.post-subcategories.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateSubcategory($request);

        PostSubcategory::create($data);

        return redirect()
            ->route('admin.post-subcategories.index')
            ->with('success', 'Sub category created successfully.');
    }

    public function edit(PostSubcategory $postSubcategory): View
    {
        $categories = PostCategory::orderBy('name')->get();

        return view('back.pages.post-subcategories.edit', compact('postSubcategory', 'categories'));
    }

    public function update(Request $request, PostSubcategory $postSubcategory): RedirectResponse
    {
        $data = $this->validateSubcategory($request, $postSubcategory);

        $postSubcategory->update($data);

        return redirect()
            ->route('admin.post-subcategories.edit', $postSubcategory)
            ->with('success', 'Sub category updated successfully.');
    }

    public function destroy(PostSubcategory $postSubcategory): RedirectResponse
    {
        $postSubcategory->delete();

        return redirect()
            ->route('admin.post-subcategories.index')
            ->with('success', 'Sub category deleted successfully.');
    }

    protected function validateSubcategory(Request $request, ?PostSubcategory $subcategory = null): array
    {
        $subcategoryId = $subcategory?->id;

        $data = $request->validate([
            'post_category_id' => ['required', Rule::exists('post_categories', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('post_subcategories', 'slug')->ignore($subcategoryId)],
            'description' => ['nullable', 'string'],
        ]);

        $data['slug'] = $this->prepareSlug($data['slug'] ?? null, $data['name'], $subcategoryId);

        return $data;
    }

    protected function prepareSlug(?string $slug, string $name, ?int $ignoreId = null): string
    {
        if (!empty($slug)) {
            $slug = Str::slug($slug);
            return PostSubcategory::generateUniqueSlug($slug, $ignoreId);
        }

        return PostSubcategory::generateUniqueSlug($name, $ignoreId);
    }
}
