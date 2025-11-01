<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SitemapController extends Controller
{
    /**
     * sitemap.xml (মূল ইনডেক্স ফাইল) দেখান।
     */
    public function index(): Response
    {
        // (পরিবর্তন) $itemsPerPage এবং post_count বাদ দেওয়া হয়েছে
        $postGroups = Post::query()
            ->where('is_indexable', true)
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('MAX(updated_at) as lastmod')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $categoryLastUpdated = Category::query()
            ->orderByDesc('updated_at')
            ->value('updated_at');

        return response()
            ->view('front.sitemap-index', [
                'postGroups' => $postGroups, // শুধু মাস গ্রুপ পাস করুন
                'categoryLastUpdated' => $categoryLastUpdated,
                // '$itemsPerPage' পাস করা বন্ধ করা হয়েছে
            ])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * sitemap-posts-{year}-{month}.xml (পোস্টের লিস্ট) দেখান।
     * (পরিবর্তন) $page প্যারামিটার এবং pagination লজিক সরানো হয়েছে
     */
    public function posts(string $year, string $month): Response
    {
        $posts = Post::query()
            ->where('is_indexable', true)
            ->with('category')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderByDesc('updated_at')
            ->get(); // সব পোস্ট আনুন (skip/take ছাড়া)

        if ($posts->isEmpty()) {
            abort(404);
        }

        return response()
            ->view('front.sitemap-posts', [
                'posts' => $posts,
            ])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * sitemap-categories.xml (ক্যাটাগরির লিস্ট) দেখান।
     */
    public function categories(): Response
    {
        $categories = Category::query()
            ->orderByDesc('updated_at')
            ->get();

        return response()
            ->view('front.sitemap-categories', [
                'categories' => $categories,
            ])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * sitemap-pages.xml (স্ট্যাটিক পেইজের লিস্ট) দেখান।
     */
    public function pages(): Response
    {
        $pages = [
            ['url' => route('home'), 'lastmod' => now()->subDay()],
            ['url' => route('polls.index'), 'lastmod' => now()->subWeek()],
        ];

        return response()
            ->view('front.sitemap-pages', [
                'pages' => $pages,
            ])
            ->header('Content-Type', 'application/xml');
    }
}
