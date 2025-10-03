<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $settings = $this->settings();

        $postsQuery = Post::query()
            ->with(['category', 'author'])
            ->where('is_indexable', true)
            ->latest('created_at');

        $search = trim((string) $request->query('search'));

        if ($search !== '') {
            $postsQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('meta_title', 'like', "%{$search}%")
                    ->orWhere('meta_description', 'like', "%{$search}%");
            });
        }

        $posts = $postsQuery->paginate(9)->withQueryString();

        $seo = [
            'title' => $settings?->site_title ?? config('app.name'),
            'description' => $settings?->site_meta_description ?? $settings?->site_description ?? config('app.name') . ' blog and news updates.',
            'keywords' => $settings?->site_meta_keywords,
            'type' => 'website',
            'canonical' => route('home'),
            'indexable' => true,
        ];

        if ($search !== '') {
            $seo['title'] = 'Search results for "' . $search . '" | ' . ($settings?->site_title ?? config('app.name'));
            $seo['description'] = 'Articles and news matching the search query "' . $search . '".';
            $seo['indexable'] = false;
        }

        return view('front.home', compact('posts', 'seo', 'settings', 'search'));
    }

    protected function settings(): ?GeneralSetting
    {
        return Cache::remember('general_settings', now()->addHour(), function () {
            return GeneralSetting::query()->first();
        });
    }
}
