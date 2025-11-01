<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SitemapController extends Controller
{
    /**
     * sitemap.xml (মূল ইনডেক্স ফাইল) দেখান।
     */
    public function index(): Response
    {
        $settings = general_settings();

        if (! ($settings?->sitemap_enabled ?? true)) {
            abort(404);
        }

        $itemsPerPage = max(1, (int) ($settings?->sitemap_items_per_page ?? 1000));

        $postGroups = Post::query()
            ->where('is_indexable', true)
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('MAX(updated_at) as lastmod'),
                DB::raw('COUNT(*) as total_posts')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($group) use ($itemsPerPage) {
                $group->pages = max(1, (int) ceil(($group->total_posts ?? 0) / $itemsPerPage));
                return $group;
            });

        $categoryLastUpdated = Category::query()
            ->orderByDesc('updated_at')
            ->value('updated_at');

        return response()
            ->view('front.sitemap-index', [
                'postGroups' => $postGroups,
                'categoryLastUpdated' => $categoryLastUpdated,
                'itemsPerPage' => $itemsPerPage,
            ])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * sitemap-posts-{year}-{month}.xml (পোস্টের লিস্ট) দেখান।
     */
    public function posts(string $year, string $month, Request $request): Response
    {
        $settings = general_settings();

        if (! ($settings?->sitemap_enabled ?? true)) {
            abort(404);
        }

        $itemsPerPage = max(1, (int) ($settings?->sitemap_items_per_page ?? 1000));
        $page = max(1, (int) $request->integer('page', 1));
        $offset = ($page - 1) * $itemsPerPage;

        $query = Post::query()
            ->where('is_indexable', true)
            ->with('category')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderByDesc('updated_at');

        $totalPosts = (clone $query)->count();

        $posts = $query
            ->skip($offset)
            ->take($itemsPerPage)
            ->get();

        if ($posts->isEmpty()) {
            abort(404);
        }

        return response()
            ->view('front.sitemap-posts', [
                'posts' => $posts,
                'currentPage' => $page,
                'totalPages' => max(1, (int) ceil($totalPosts / $itemsPerPage)),
                'itemsPerPage' => $itemsPerPage,
                'year' => $year,
                'month' => $month,
            ])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * sitemap-categories.xml (ক্যাটাগরির লিস্ট) দেখান।
     */
    public function categories(): Response
    {
        $settings = general_settings();

        if (! ($settings?->sitemap_enabled ?? true)) {
            abort(404);
        }

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
        $settings = general_settings();

        if (! ($settings?->sitemap_enabled ?? true)) {
            abort(404);
        }

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
