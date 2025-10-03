@extends('layouts.front')

@section('content')
<article class="bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <nav class="text-sm text-slate-500" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
            <ol class="flex flex-wrap items-center gap-1">
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a href="{{ route('home') }}" itemprop="item" class="hover:text-indigo-600">
                        <span itemprop="name">Home</span>
                    </a>
                    <meta itemprop="position" content="1" />
                </li>
                @if($post->category)
                    <span aria-hidden="true">/</span>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="name">{{ $post->category->name }}</span>
                        <meta itemprop="position" content="2" />
                    </li>
                    <span aria-hidden="true">/</span>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="name">{{ $post->title }}</span>
                        <meta itemprop="position" content="3" />
                    </li>
                @else
                    <span aria-hidden="true">/</span>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="name">{{ $post->title }}</span>
                        <meta itemprop="position" content="2" />
                    </li>
                @endif
            </ol>
        </nav>

        <header class="mt-6">
            <h1 class="text-3xl font-bold text-slate-900 sm:text-4xl">{{ $post->title }}</h1>
            <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-slate-500">
                @if($post->author)
                    <span>By <span class="font-medium text-slate-700">{{ $post->author->name }}</span></span>
                @endif
                <span aria-hidden="true">•</span>
                <time datetime="{{ optional($post->created_at)->toDateString() }}">Published {{ optional($post->created_at)->format('F d, Y') }}</time>
                @if($post->updated_at && $post->updated_at->ne($post->created_at))
                    <span aria-hidden="true">•</span>
                    <time datetime="{{ optional($post->updated_at)->toDateString() }}">Updated {{ optional($post->updated_at)->format('F d, Y') }}</time>
                @endif
            </div>
        </header>

        @if ($post->isVideo() && $post->video_embed_html)
            <div class="mt-8">
                {!! $post->video_embed_html !!}
            </div>
        @elseif ($post->thumbnail_url)
            <figure class="mt-8">
                <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="w-full rounded-xl object-cover shadow-lg">
            </figure>
        @endif

        <div class="prose prose-slate max-w-none mt-8">
            {!! $post->description !!}
        </div>

        <section class="mt-12 rounded-xl border border-slate-200 bg-slate-50 p-6">
            <h2 class="text-lg font-semibold text-slate-800">Enjoyed this story?</h2>
            <p class="mt-2 text-sm text-slate-600">Share it with your network to help others discover insightful updates.</p>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($seo['canonical']) }}" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700" target="_blank" rel="noopener">
                    Share on Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode($seo['canonical']) }}&text={{ urlencode($post->title) }}" class="inline-flex items-center rounded-lg bg-sky-500 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-600" target="_blank" rel="noopener">
                    Share on X
                </a>
                <a href="https://www.linkedin.com/shareArticle?url={{ urlencode($seo['canonical']) }}&title={{ urlencode($post->title) }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700" target="_blank" rel="noopener">
                    Share on LinkedIn
                </a>
            </div>
        </section>
    </div>
</article>
@endsection

@push('meta')
    @php
        $logoPath = $settings?->site_logo;
        $logoUrl = $logoPath
            ? (\Illuminate\Support\Str::startsWith($logoPath, ['http://', 'https://'])
                ? $logoPath
                : \Illuminate\Support\Facades\Storage::url($logoPath))
            : asset('favicon.ico');

        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $post->title,
            'datePublished' => $seo['published_time'] ?? optional($post->created_at)->toIso8601String(),
            'dateModified' => $seo['modified_time'] ?? optional($post->updated_at ?? $post->created_at)->toIso8601String(),
            'author' => $post->author ? [
                '@type' => 'Person',
                'name' => $post->author->name,
            ] : null,
            'publisher' => [
                '@type' => 'Organization',
                'name' => $settings->site_title ?? config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $logoUrl,
                ],
            ],
            'image' => array_filter([$seo['image'] ?? null]),
            'mainEntityOfPage' => $seo['canonical'] ?? route('posts.show', $post),
            'articleSection' => $post->category?->name,
            'description' => $seo['description'] ?? $post->excerpt,
        ];

        $articleSchema = array_filter($articleSchema, fn ($value) => !is_null($value));

        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => route('home'),
                ],
            ],
        ];

        if ($post->category) {
            $breadcrumbSchema['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $post->category->name,
            ];
            $breadcrumbSchema['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $post->title,
                'item' => $seo['canonical'] ?? route('posts.show', $post),
            ];
        } else {
            $breadcrumbSchema['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $post->title,
                'item' => $seo['canonical'] ?? route('posts.show', $post),
            ];
        }
    @endphp
    <script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
