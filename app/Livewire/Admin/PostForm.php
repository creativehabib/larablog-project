<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Post;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class PostForm extends Component
{
    use WithFileUploads;

    public ?Post $post = null;

    public ?string $title = '';
    public ?string $slug = '';
    public ?string $description = '';
    public ?string $category_id = '';
    public ?string $sub_category_id = '';
    public bool $is_featured = false;
    public bool $allow_comments = true;
    public bool $is_indexable = true;
    public ?string $meta_title = null;
    public ?string $meta_description = null;
    public ?string $meta_keywords = null;
    public $thumbnail;
    public ?string $existingThumbnail = null;

    public bool $autoGenerateSlug = true;

    protected ?string $lastSyncedDescription = null;

    public function mount(?Post $post = null): void
    {

        $this->post = $post;

        if ($post) {
            $this->title = $post->title;
            $this->slug = $post->slug;
            $this->description = $post->description;
            $this->category_id = (string) $post->category_id;
            $this->sub_category_id = $post->sub_category_id ? (string) $post->sub_category_id : '';
            $this->is_featured = (bool) $post->is_featured;
            $this->allow_comments = (bool) $post->allow_comments;
            $this->is_indexable = (bool) $post->is_indexable;
            $this->meta_title = $post->meta_title;
            $this->meta_description = $post->meta_description;
            $this->meta_keywords = $post->meta_keywords;
            $this->existingThumbnail = $post->thumbnail_path;
            $this->autoGenerateSlug = false;
        }

        $this->lastSyncedDescription = $this->description;
    }

    public function updatedTitle($value): void
    {
        if ($this->autoGenerateSlug) {
            $this->slug = $this->generateUniqueSlug($value);
        }
    }

    public function updatedSlug($value): void
    {
        $this->autoGenerateSlug = false;
        $this->slug = $this->generateUniqueSlug($value ?: $this->title);
    }

    public function updatedCategoryId($value): void
    {
        if ($value === null || $value === '') {
            $this->sub_category_id = '';
            return;
        }

        if ($this->sub_category_id) {
            $isValid = SubCategory::where('id', $this->sub_category_id)
                ->where('category_id', $value)
                ->exists();

            if (! $isValid) {
                $this->sub_category_id = '';
            }
        }
    }

    public function removeExistingThumbnail(): void
    {
        if (! $this->existingThumbnail) {
            return;
        }

        Storage::disk('public')->delete($this->existingThumbnail);
        $this->existingThumbnail = null;

        if ($this->post) {
            $this->post->update(['thumbnail_path' => null]);
        }

        $this->dispatch('showToastr', type: 'success', message: 'Thumbnail removed successfully.');
    }

    public function save(): mixed
    {

        $data = $this->validate($this->rules());

        if (blank($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($this->title);
        }

        if (! $this->category_id) {
            $this->addError('category_id', 'Please select a category.');
            return null;
        }

        $data['category_id'] = (int) $this->category_id;
        $data['sub_category_id'] = $this->sub_category_id ? (int) $this->sub_category_id : null;

        if ($data['sub_category_id']) {
            $isValidSubCategory = SubCategory::where('id', $data['sub_category_id'])
                ->where('category_id', $data['category_id'])
                ->exists();

            if (! $isValidSubCategory) {
                $this->addError('sub_category_id', 'Selected sub category does not belong to the chosen category.');
                return null;
            }
        }

        $data['is_featured'] = $this->is_featured;
        $data['allow_comments'] = $this->allow_comments;
        $data['is_indexable'] = $this->is_indexable;
        $data['description'] = $this->description;
        $data['meta_title'] = $this->meta_title;
        $data['meta_description'] = $this->meta_description;
        $data['meta_keywords'] = $this->meta_keywords;

        if ($this->thumbnail) {
            if ($this->existingThumbnail) {
                Storage::disk('public')->delete($this->existingThumbnail);
            }

            $data['thumbnail_path'] = $this->thumbnail->store('posts', 'public');
        }

        unset($data['thumbnail']);

        if ($this->post && $this->post->exists) {
            $this->post->update($data);
            $message = 'Post updated successfully.';
        } else {
            $userId = Auth::id();

            if (! $userId) {
                abort(403);
            }

            $data['user_id'] = $userId;

            $this->post = Post::create($data);
            $message = 'Post created successfully.';
        }

        $this->dispatch('showToastr', type: 'success', message: $message);

        return redirect()->route('admin.posts.index')->with('success', $message);
    }

    protected function rules(): array
    {
        $postId = $this->post?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('posts', 'slug')->ignore($postId),
            ],
            'description' => ['required', 'string'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'sub_category_id' => ['nullable', 'integer', Rule::exists('sub_categories', 'id')],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
        ];
    }

    protected function generateUniqueSlug(?string $value): string
    {
        $base = Str::slug($value ?? '');

        if ($base === '') {
            return '';
        }

        $slug = $base;
        $counter = 2;

        while ($this->slugExists($slug)) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    protected function slugExists(string $slug): bool
    {
        return Post::when($this->post, fn ($query) => $query->where('id', '!=', $this->post->id))
            ->where('slug', $slug)
            ->exists();
    }

    #[Computed]
    public function categories()
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get();

        if ($this->category_id && ! $categories->contains('id', (int) $this->category_id)) {
            $selectedCategory = Category::find($this->category_id);

            if ($selectedCategory) {
                $categories->push($selectedCategory);
                $categories = $categories->sortBy('name')->values();
            }
        }

        return $categories;
    }

    #[Computed]
    public function availableSubCategories()
    {
        if (! $this->category_id) {
            return collect();
        }

        $subCategories = SubCategory::query()
            ->where('category_id', $this->category_id)
            ->orderBy('name')
            ->get();

        if ($this->sub_category_id && ! $subCategories->contains('id', (int) $this->sub_category_id)) {
            $selectedSubCategory = SubCategory::find($this->sub_category_id);

            if ($selectedSubCategory && (int) $selectedSubCategory->category_id === (int) $this->category_id) {
                $subCategories->push($selectedSubCategory);
                $subCategories = $subCategories->sortBy('name')->values();
            }
        }

        return $subCategories;
    }

    public function render()
    {
        if ($this->lastSyncedDescription !== $this->description) {
            $this->dispatch('syncPostEditor', $this->description ?? '');
            $this->lastSyncedDescription = $this->description;
        }

        return view('livewire.admin.post-form');
    }
}
