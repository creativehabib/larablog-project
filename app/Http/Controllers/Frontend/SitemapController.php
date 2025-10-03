<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $posts = Post::query()
            ->where('is_indexable', true)
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at', 'created_at']);

        $lastUpdated = optional($posts->first())->updated_at ?? now();

        return response()
            ->view('front.sitemap', [
                'posts' => $posts,
                'lastUpdated' => $lastUpdated,
            ])
            ->header('Content-Type', 'application/xml');
    }
}
