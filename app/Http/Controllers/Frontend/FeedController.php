<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class FeedController extends Controller
{
    public function __invoke(): Response
    {
        $settings = $this->settings();

        $posts = Post::query()
            ->with('author')
            ->where('is_indexable', true)
            ->latest('created_at')
            ->take(20)
            ->get();

        $updated = optional($posts->first())->updated_at ?? now();

        return response()
            ->view('front.feed', [
                'posts' => $posts,
                'settings' => $settings,
                'updated' => $updated,
            ])
            ->header('Content-Type', 'application/rss+xml');
    }

    protected function settings(): ?GeneralSetting
    {
        return Cache::remember('general_settings', now()->addHour(), function () {
            return GeneralSetting::query()->first();
        });
    }
}
