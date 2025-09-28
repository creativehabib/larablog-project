<?php

namespace App\Http\Controllers;

use App\Models\PostCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PostCategoryController extends Controller
{
    public function index(): View
    {
        $categories = PostCategory::query()->withCount(['posts', 'subcategories'])->orderBy('name')->paginate(15);

        return view('back.pages.post-categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('back.pages.post-categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateCategory($request);

        PostCategory::create($data);

        return redirect()
            ->route('admin.post-categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(PostCategory $postCategory): View
    {
        return view('back.pages.post-categories.edit', compact('postCategory'));
    }

    public function update(Request $request, PostCategory $postCategory): RedirectResponse
    {
        $data = $this->validateCategory($request, $postCategory);

        $postCategory->update($data);

        return redirect()
            ->route('admin.post-categories.edit', $postCategory)
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(PostCategory $postCategory): RedirectResponse
    {
        $postCategory->delete();

        return redirect()
            ->route('admin.post-categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    protected function validateCategory(Request $request, ?PostCategory $category = null): array
    {
        $categoryId = $category?->id;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('post_categories', 'slug')->ignore($categoryId)],
            'description' => ['nullable', 'string'],
        ]);

        $data['slug'] = $this->prepareSlug($data['slug'] ?? null, $data['name'], $categoryId);

        return $data;
    }

    protected function prepareSlug(?string $slug, string $name, ?int $ignoreId = null): string
    {
        if (!empty($slug)) {
            $slug = Str::slug($slug);
            return PostCategory::generateUniqueSlug($slug, $ignoreId);
        }

        return PostCategory::generateUniqueSlug($name, $ignoreId);
    }
}
