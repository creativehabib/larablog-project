<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubCategoryController extends Controller
{
    public function index(): View
    {
        $subCategories = SubCategory::with('category')->latest()->paginate(15);

        return view('back.pages.sub_categories.index', [
            'pageTitle' => 'Sub Categories',
            'subCategories' => $subCategories,
        ]);
    }

    public function create(): View
    {
        return view('back.pages.sub_categories.create', [
            'pageTitle' => 'Create Sub Category',
            'categories' => Category::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $slug = Str::slug($validated['slug'] ?? $validated['name']);
        if (empty($slug)) {
            $slug = Str::random(8);
        }
        $slug = $this->makeUniqueSlug($slug);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('sub-categories', 'public');
        }

        SubCategory::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('admin.subcategories.index')->with('success', 'Sub category created successfully.');
    }

    public function edit(SubCategory $subcategory): View
    {
        return view('back.pages.sub_categories.edit', [
            'pageTitle' => 'Edit Sub Category',
            'subcategory' => $subcategory,
            'categories' => Category::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(Request $request, SubCategory $subcategory): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $slug = Str::slug($validated['slug'] ?? $validated['name']);
        if (empty($slug)) {
            $slug = Str::random(8);
        }
        $slug = $this->makeUniqueSlug($slug, $subcategory->id);

        $imagePath = $subcategory->image_path;
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('sub-categories', 'public');
        }

        $subcategory->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('admin.subcategories.index')->with('success', 'Sub category updated successfully.');
    }

    public function destroy(SubCategory $subcategory): RedirectResponse
    {
        if ($subcategory->image_path && Storage::disk('public')->exists($subcategory->image_path)) {
            Storage::disk('public')->delete($subcategory->image_path);
        }

        $subcategory->delete();

        return redirect()->route('admin.subcategories.index')->with('success', 'Sub category deleted successfully.');
    }

    protected function makeUniqueSlug(string $baseSlug, ?int $ignoreId = null): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $baseSlug.'-'.$counter++;
        }

        return $slug;
    }

    protected function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        return SubCategory::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }
}
