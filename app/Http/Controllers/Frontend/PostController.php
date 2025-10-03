<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function show(Post $post)
    {
        $post->loadMissing(['category', 'author', 'playlist']);

        $settings = $this->settings();

        $image = $post->thumbnail_path ? Storage::url($post->thumbnail_path) : null;

        $seo = [
            'title' => $post->meta_title ?: $post->title,
            'description' => $post->meta_description ?: $post->excerpt,
            'keywords' => $post->meta_keywords ?: $settings?->site_meta_keywords,
            'type' => 'article',
            'canonical' => route('posts.show', $post),
            'indexable' => (bool) $post->is_indexable,
            'image' => $image,
            'published_time' => optional($post->created_at)->toIso8601String(),
            'modified_time' => optional($post->updated_at ?? $post->created_at)->toIso8601String(),
            'author' => $post->author?->name,
        ];

        if ($post->isVideo()) {
            $seo['type'] = 'video.other';
            $seo['video'] = $post->video_embed_url;
            $seo['twitter_card'] = 'player';
            if ($post->video_duration) {
                $seo['video_duration'] = $post->video_duration;
            }
            if ($post->playlist) {
                $seo['video_playlist'] = $post->playlist->name;
            }
        }

        return view('front.post', compact('post', 'seo', 'settings'));
    }

    protected function settings(): ?GeneralSetting
    {
        return Cache::remember('general_settings', now()->addHour(), function () {
            return GeneralSetting::query()->first();
        });
    }
}
