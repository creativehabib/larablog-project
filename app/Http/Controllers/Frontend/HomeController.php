<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\Poll;
use App\Models\Post;
use App\Support\BanglaFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $settings = $this->settings();

        $search = trim((string) $request->query('search'));
        $categorySlug = trim((string) $request->query('category'));
        $activeCategory = null;

        $postsQuery = Post::query()
            ->with(['category', 'author', 'playlist'])
            ->where('is_indexable', true)
            ->latest('created_at');

        if ($categorySlug !== '') {
            $activeCategory = Category::query()->where('slug', $categorySlug)->first();

            if ($activeCategory) {
                $postsQuery->where('category_id', $activeCategory->id);
            }
        }

        if ($search !== '') {
            $postsQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('meta_title', 'like', "%{$search}%")
                    ->orWhere('meta_description', 'like', "%{$search}%");
            });
        }

        $postsForSections = (clone $postsQuery)->take(20)->get();

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
            ->take(6)
            ->values();

        $latestPosts = (clone $postsQuery)->paginate(12)->withQueryString();

        $trendingTopics = Category::query()
            ->withCount(['posts as published_posts_count' => fn ($query) => $query->where('is_indexable', true)])
            ->having('published_posts_count', '>', 0)
            ->orderByDesc('published_posts_count')
            ->take(8)
            ->get();

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

        $activePoll = Poll::query()
            ->where('is_active', true)
            ->orderByDesc('poll_date')
            ->orderByDesc('created_at')
            ->first();

        $currentDate = now(config('app.timezone', 'Asia/Dhaka'));
        $currentDateBangla = BanglaFormatter::fullDate($currentDate);
        $currentTimeBangla = BanglaFormatter::time($currentDate);

        $seo = [
            'title' => $settings?->site_title ?? config('app.name'),
            'description' => $settings?->site_meta_description ?? $settings?->site_description ?? config('app.name') . ' blog and news updates.',
            'keywords' => $settings?->site_meta_keywords,
            'type' => 'website',
            'canonical' => route('home'),
            'indexable' => true,
        ];

        if ($activeCategory) {
            $seo['title'] = $activeCategory->name . ' সংবাদ | ' . ($settings?->site_title ?? config('app.name'));
            $seo['description'] = $activeCategory->description
                ? strip_tags($activeCategory->description)
                : $activeCategory->name . ' বিভাগের সর্বশেষ সংবাদ, বিশ্লেষণ এবং আপডেট।';
        }

        if ($search !== '') {
            $seo['title'] = '“' . $search . '” সম্পর্কিত সংবাদ | ' . ($settings?->site_title ?? config('app.name'));
            $seo['description'] = '“' . $search . '” অনুসন্ধানের সাথে মিল থাকা সংবাদসমূহ।';
            $seo['indexable'] = false;
        }

        $canonicalParams = [];

        if ($activeCategory) {
            $canonicalParams['category'] = $activeCategory->slug;
        }

        if ($search !== '') {
            $canonicalParams['search'] = $search;
        }

        if (! empty($canonicalParams)) {
            $seo['canonical'] = route('home', $canonicalParams);
        }

        return view('front.index', [
            'settings' => $settings,
            'seo' => $seo,
            'search' => $search,
            'activeCategory' => $activeCategory,
            'leadStory' => $leadStory,
            'topStories' => $topStories,
            'moreStories' => $moreStories,
            'latestPosts' => $latestPosts,
            'trendingTopics' => $trendingTopics,
            'categorySections' => $categorySections,
            'latestVideos' => $latestVideos,
            'activePoll' => $activePoll,
            'currentDateBangla' => $currentDateBangla,
            'currentTimeBangla' => $currentTimeBangla,
        ]);
    }

    protected function settings(): ?GeneralSetting
    {
        return Cache::remember('general_settings', now()->addHour(), function () {
            return GeneralSetting::query()->first();
        });
    }
}
