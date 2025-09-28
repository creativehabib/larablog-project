<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\PostSubcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::with(['category', 'subcategory'])->latest()->paginate(12);

        return view('back.pages.posts.index', compact('posts'));
    }

    public function create(): View
    {
        $categories = PostCategory::with('subcategories')->orderBy('name')->get();

        return view('back.pages.posts.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePost($request);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('posts', 'public');
        }

        if (empty($data['excerpt'])) {
            $data['excerpt'] = Str::limit(strip_tags($data['body']), 250) ?: null;
        }

        Post::create($data);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post created successfully.');
    }

    public function edit(Post $post): View
    {
        $categories = PostCategory::with('subcategories')->orderBy('name')->get();

        return view('back.pages.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $data = $this->validatePost($request, $post);

        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail_path) {
                Storage::disk('public')->delete($post->thumbnail_path);
            }

            $data['thumbnail_path'] = $request->file('thumbnail')->store('posts', 'public');
        }

        if (empty($data['excerpt'])) {
            $data['excerpt'] = Str::limit(strip_tags($data['body']), 250) ?: null;
        }

        $post->update($data);

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        if ($post->thumbnail_path) {
            Storage::disk('public')->delete($post->thumbnail_path);
        }

        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post deleted successfully.');
    }

    /**
     * @throws ValidationException
     */
    protected function validatePost(Request $request, ?Post $post = null): array
    {
        $postId = $post?->id;

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('posts', 'slug')->ignore($postId)],
            'post_category_id' => ['required', Rule::exists('post_categories', 'id')],
            'post_subcategory_id' => ['nullable', Rule::exists('post_subcategories', 'id')],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'is_featured' => ['sometimes', 'boolean'],
            'allow_comments' => ['sometimes', 'boolean'],
            'is_indexable' => ['sometimes', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
        ]);

        if (!empty($data['post_subcategory_id'])) {
            $subcategory = PostSubcategory::query()
                ->where('id', $data['post_subcategory_id'])
                ->where('post_category_id', $data['post_category_id'])
                ->first();

            if (!$subcategory) {
                throw ValidationException::withMessages([
                    'post_subcategory_id' => 'The selected sub category does not belong to the chosen category.',
                ]);
            }
        }

        $data['is_featured'] = $request->boolean('is_featured');
        $data['allow_comments'] = $request->boolean('allow_comments');
        $data['is_indexable'] = $request->boolean('is_indexable');

        $data['slug'] = $this->prepareSlug($data['slug'] ?? null, $data['title'], $postId);

        return $data;
    }

    protected function prepareSlug(?string $slug, string $title, ?int $ignoreId = null): string
    {
        if (!empty($slug)) {
            $slug = Str::slug($slug);
            return Post::generateUniqueSlug($slug, $ignoreId);
        }

        return Post::generateUniqueSlug($title, $ignoreId);
    }
}
