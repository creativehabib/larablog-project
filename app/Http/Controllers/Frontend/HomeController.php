<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // settings() মেথডটি রাখা হলো, কারণ SEO লজিকের জন্য $settings দরকার
        $settings = $this->settings();

        $search = trim((string) $request->query('search'));
        $categorySlug = trim((string) $request->query('category'));
        $activeCategory = null;

        $postsQuery = Post::query()
            ->with(['category', 'author', 'playlist'])
            ->where('is_indexable', true)
            ->latest('created_at');

        // ... [categorySlug এবং search-এর লজিক এখানে থাকবে] ...

        $postsForSections = (clone $postsQuery)->take(24)->get();

        $leadStory = $postsForSections->firstWhere('is_featured', true) ?? $postsForSections->first();

        $topStories = $postsForSections
            ->filter(fn ($post) => ! $leadStory || $post->id !== $leadStory->id)
            ->take(3)
            ->values();

        $highlightedIds = $topStories->pluck('id');

        if ($leadStory) {
            $highlightedIds->push($leadStory->id);
        }

        $moreStories = $postsForSections
            ->filter(fn ($post) => ! $highlightedIds->contains($post->id))
            ->values()
            ->take(8)
            ->values();

        // শুধুমাত্র হোমপেজের জন্য প্যাজিনেটেড পোস্ট
        $latestPosts = (clone $postsQuery)->paginate(12)->withQueryString();

        // **গুরুত্বপূর্ণ:** সমস্ত শেয়ার্ড ডেটা লোড করার কোড (sidebarPosts, popularPosts, navCategories ইত্যাদি)
        // এই ফাইল থেকে **মুছে ফেলা হয়েছে** এবং `AppServiceProvider.php`-এ চলে গেছে।

        $categorySections = Category::query()
            ->withCount(['posts as published_posts_count' => fn ($query) => $query->where('is_indexable', true)])
            ->having('published_posts_count', '>', 0)
            ->orderByDesc('published_posts_count')
            ->take(4)
            ->with(['posts' => function ($query) {
                $query->with(['author', 'category'])
                    ->where('is_indexable', true)
                    ->latest('created_at')
                    ->take(5);
            }])
            ->get();

        $latestVideos = Post::query()
            ->with(['category', 'author'])
            ->where('is_indexable', true)
            ->where('content_type', Post::CONTENT_TYPE_VIDEO)
            ->latest('created_at')
            ->take(4)
            ->get();

        // --- SEO লজিক ---
        $seo = [
            'title' => $settings?->site_title ?? config('app.name'),
            // ... (বাকি SEO লজিক)
        ];
        // ... (activeCategory এবং search এর জন্য SEO লজিক এখানে থাকবে) ...


        // --- ভিউতে ডেটা পাস ---
        return view('front.index', [
            'settings' => $settings,
            'seo' => $seo,
            'search' => $search,
            'activeCategory' => $activeCategory,
            'leadStory' => $leadStory,
            'topStories' => $topStories,
            'moreStories' => $moreStories,
            'latestPosts' => $latestPosts,
            'categorySections' => $categorySections,
            'latestVideos' => $latestVideos,
        ]);
    }

    protected function settings(): ?GeneralSetting
    {
        return Cache::remember('general_settings', now()->addHour(), function () {
            return GeneralSetting::query()->first();
        });
    }
}
