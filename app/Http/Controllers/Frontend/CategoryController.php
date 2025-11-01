<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        return $this->renderCategory($category);
    }

    public function showCategory(Category $category)
    {
        return $this->renderCategory($category);
    }

    protected function renderCategory(Category $category)
    {
        $settings = $this->settings();

        $postsQuery = Post::query()
            ->with(['category', 'author', 'playlist'])
            ->where('is_indexable', true)
            ->where('category_id', $category->id)
            ->latest('created_at');

        $postsForLayout = (clone $postsQuery)->take(24)->get();

        $leadStory = $postsForLayout->firstWhere('is_featured', true) ?? $postsForLayout->first();

        $topStories = $postsForLayout
            ->filter(fn ($post) => ! $leadStory || $post->id !== $leadStory->id)
            ->take(3)
            ->values();

        $highlightedIds = $topStories->pluck('id');

        if ($leadStory) {
            $highlightedIds->push($leadStory->id);
        }

        $moreStories = $postsForLayout
            ->filter(fn ($post) => ! $highlightedIds->contains($post->id))
            ->values()
            ->take(8)
            ->values();

        $latestPosts = (clone $postsQuery)->paginate(12)->withQueryString();

        $seo = [
            'title' => $category->name.' - '.($settings?->site_title ?? config('app.name')),
            'description' => $category->description ?: ($settings?->site_meta_description ?? ''),
            'keywords' => $settings?->site_meta_keywords,
            'canonical' => route('categories.show', $category),
            'type' => 'website',
            'indexable' => true,
        ];

        return view('front.category', [
            'category' => $category,
            'seo' => $seo,
            'settings' => $settings,
            'leadStory' => $leadStory,
            'topStories' => $topStories,
            'moreStories' => $moreStories,
            'latestPosts' => $latestPosts,
            'activeCategory' => $category,
        ]);
    }

    protected function settings(): ?GeneralSetting
    {
        return Cache::remember('general_settings', now()->addHour(), function () {
            return GeneralSetting::query()->first();
        });
    }
}
