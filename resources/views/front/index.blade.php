@extends('layouts.front')

@php
    use App\Support\BanglaFormatter;
@endphp

@section('content')
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">ঢাকা, বাংলাদেশ</p>
                    <p class="mt-1 text-xl font-bold text-slate-900">{{ $currentDateBangla }}</p>
                    @if($currentTimeBangla !== '')
                        <p class="mt-1 text-xs text-slate-500">সময়: {{ $currentTimeBangla }}</p>
                    @endif
                </div>
                <div class="space-y-2 text-sm text-slate-600 md:text-right">
                    @if($search !== '')
                        <p>“{{ $search }}” অনুসন্ধানের ফলাফল প্রদর্শিত হচ্ছে।</p>
                    @elseif($activeCategory)
                        <p>বর্তমানে প্রদর্শিত বিভাগ: <span class="font-semibold text-slate-900">{{ $activeCategory->name }}</span></p>
                    @else
                        <p>সর্বশেষ সংবাদ, বিশ্লেষণ ও ভিডিও আপডেট এক নজরে।</p>
                    @endif
                    <div class="flex flex-wrap gap-2 md:justify-end">
                        @if($activeCategory)
                            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-600 hover:border-indigo-500 hover:text-indigo-600">
                                <i class="fas fa-times text-[10px]"></i> ফিল্টার অপসারণ
                            </a>
                        @endif
                        <a href="{{ route('polls.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-600 hover:border-indigo-500 hover:text-indigo-600">
                            <i class="fas fa-poll text-[10px]"></i> মতামত জরিপ
                        </a>
                        <a href="{{ route('feed') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-600 hover:border-indigo-500 hover:text-indigo-600">
                            <i class="fas fa-rss text-[10px]"></i> RSS ফিড
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($trendingTopics->isNotEmpty())
        <section class="border-b border-slate-200 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center gap-4 overflow-x-auto">
                <span class="inline-flex items-center rounded-full bg-rose-600 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-white">ট্রেন্ডিং</span>
                <div class="flex items-center gap-3">
                    @foreach($trendingTopics as $topic)
                        <a href="{{ route('home', ['category' => $topic->slug]) }}" class="whitespace-nowrap rounded-full border border-slate-200 bg-white px-3 py-1 text-sm font-medium text-slate-700 hover:border-indigo-500 hover:text-indigo-600">
                            {{ $topic->name }}
                            <span class="ml-1 text-xs text-slate-400">({{ BanglaFormatter::digits($topic->published_posts_count ?? $topic->posts_count ?? $topic->posts?->count() ?? 0) }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid gap-8 lg:grid-cols-[2fr,1fr]">
            <div>
                @if($leadStory)
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        @if($leadStory->thumbnail_url)
                            <a href="{{ route('posts.show', $leadStory) }}" class="block">
                                <img src="{{ $leadStory->thumbnail_url }}" alt="{{ $leadStory->title }}" class="aspect-[16/9] w-full object-cover">
                            </a>
                        @endif
                        <div class="p-6">
                            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wide text-indigo-600">
                                @if($leadStory->category)
                                    <a href="{{ route('home', ['category' => $leadStory->category->slug]) }}" class="hover:text-indigo-700">{{ $leadStory->category->name }}</a>
                                @endif
                                @if($leadStory->created_at)
                                    <span class="text-slate-400">•</span>
                                    <time datetime="{{ optional($leadStory->created_at)->toDateString() }}" class="text-slate-500">{{ BanglaFormatter::shortDate($leadStory->created_at) }}</time>
                                @endif
                                @if($leadStory->isVideo())
                                    <span class="text-slate-400">•</span>
                                    <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[11px] font-semibold text-rose-600">ভিডিও</span>
                                @endif
                            </div>
                            <h1 class="mt-4 text-3xl font-bold leading-tight text-slate-900 sm:text-4xl">
                                <a href="{{ route('posts.show', $leadStory) }}" class="hover:text-indigo-600">{{ $leadStory->title }}</a>
                            </h1>
                            <p class="mt-4 text-base text-slate-600">{{ $leadStory->excerpt }}</p>
                            <div class="mt-6 flex flex-wrap items-center gap-4 text-sm">
                                <a href="{{ route('posts.show', $leadStory) }}" class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-4 py-2 font-semibold text-white hover:bg-indigo-700">
                                    {{ $leadStory->isVideo() ? 'ভিডিও দেখুন' : 'সম্পূর্ণ খবর পড়ুন' }}
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                                @if($leadStory->author)
                                    <span class="text-slate-500">রিপোর্ট: <span class="font-medium text-slate-700">{{ $leadStory->author->name }}</span></span>
                                @endif
                            </div>
                        </div>
                    </article>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center text-slate-500">
                        বর্তমানে কোনো সংবাদ পাওয়া যায়নি।
                    </div>
                @endif
            </div>
            <div class="space-y-6">
                @if($topStories->isNotEmpty())
                    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-200 px-5 py-3">
                            <h2 class="text-lg font-semibold text-slate-900">শীর্ষ খবর</h2>
                        </div>
                        <ul class="divide-y divide-slate-200">
                            @foreach($topStories as $story)
                                <li class="px-5 py-4 hover:bg-slate-50">
                                    <article class="flex gap-4">
                                        @if($story->thumbnail_url)
                                            <a href="{{ route('posts.show', $story) }}" class="flex-shrink-0">
                                                <img src="{{ $story->thumbnail_url }}" alt="{{ $story->title }}" class="h-16 w-24 rounded-lg object-cover">
                                            </a>
                                        @endif
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-indigo-600">
                                                @if($story->category)
                                                    <a href="{{ route('home', ['category' => $story->category->slug]) }}" class="hover:text-indigo-700">{{ $story->category->name }}</a>
                                                @endif
                                                @if($story->created_at)
                                                    <span class="text-slate-400">•</span>
                                                    <time datetime="{{ optional($story->created_at)->toDateString() }}" class="text-slate-500">{{ BanglaFormatter::shortDate($story->created_at) }}</time>
                                                @endif
                                            </div>
                                            <h3 class="mt-2 text-base font-semibold text-slate-900">
                                                <a href="{{ route('posts.show', $story) }}" class="hover:text-indigo-600">{{ $story->title }}</a>
                                            </h3>
                                            <p class="mt-1 text-sm text-slate-600 line-clamp-2">{{ $story->excerpt }}</p>
                                        </div>
                                    </article>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($latestVideos->isNotEmpty())
                    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-200 px-5 py-3">
                            <h2 class="text-lg font-semibold text-slate-900">ভিডিও হাইলাইট</h2>
                        </div>
                        <ul class="divide-y divide-slate-200">
                            @foreach($latestVideos as $video)
                                <li class="px-5 py-4 hover:bg-slate-50">
                                    <a href="{{ route('posts.show', $video) }}" class="flex items-center gap-4">
                                        @if($video->thumbnail_url)
                                            <span class="relative inline-flex">
                                                <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="h-16 w-24 rounded-lg object-cover">
                                                <span class="absolute inset-0 flex items-center justify-center">
                                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-black/70 text-white">
                                                        <i class="fas fa-play text-xs"></i>
                                                    </span>
                                                </span>
                                            </span>
                                        @endif
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-slate-900 line-clamp-2">{{ $video->title }}</p>
                                            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                                @if($video->playlist)
                                                    <span class="inline-flex items-center rounded bg-slate-100 px-2 py-0.5 text-slate-600">{{ $video->playlist->name }}</span>
                                                @endif
                                                @if($video->video_duration)
                                                    <span class="inline-flex items-center rounded bg-slate-100 px-2 py-0.5 text-slate-600">{{ $video->video_duration }}</span>
                                                @endif
                                                @if($video->created_at)
                                                    <time datetime="{{ optional($video->created_at)->toDateString() }}">{{ BanglaFormatter::shortDate($video->created_at) }}</time>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($activePoll)
                    <div class="rounded-xl border border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-white shadow-sm">
                        <div class="px-5 py-4">
                            <h2 class="text-lg font-semibold text-slate-900">চলমান জরিপ</h2>
                            <p class="mt-2 text-sm text-slate-600">{{ $activePoll->question }}</p>
                            <div class="mt-4 flex items-center gap-3 text-xs text-slate-500">
                                <span>মোট ভোট: <strong class="text-slate-900">{{ $activePoll->total_vote_bangla }}</strong></span>
                                @if($activePoll->poll_date_bangla)
                                    <span>•</span>
                                    <span>তারিখ: {{ $activePoll->poll_date_bangla }}</span>
                                @endif
                            </div>
                            <a href="{{ route('polls.index') }}" class="mt-4 inline-flex items-center gap-2 rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                এখনই ভোট দিন
                                <i class="fas fa-external-link-alt text-xs"></i>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if($moreStories->isNotEmpty())
        <section class="bg-slate-50 border-y border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-slate-900">নিউজরুম থেকে আরও</h2>
                    <a href="{{ route('home') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">সমস্ত সংবাদ</a>
                </div>
                <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($moreStories as $story)
                        <article class="group flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                            @if($story->thumbnail_url)
                                <a href="{{ route('posts.show', $story) }}" class="relative block aspect-[16/9] overflow-hidden bg-slate-100">
                                    <img src="{{ $story->thumbnail_url }}" alt="{{ $story->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                    @if($story->isVideo())
                                        <span class="absolute bottom-3 left-3 inline-flex items-center gap-1 rounded-full bg-black/70 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white">
                                            <i class="fas fa-play text-[10px]"></i>
                                            ভিডিও
                                        </span>
                                    @endif
                                </a>
                            @endif
                            <div class="flex flex-1 flex-col p-5">
                                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wide text-indigo-600">
                                    @if($story->category)
                                        <a href="{{ route('home', ['category' => $story->category->slug]) }}" class="hover:text-indigo-700">{{ $story->category->name }}</a>
                                    @endif
                                    @if($story->created_at)
                                        <span class="text-slate-400">•</span>
                                        <time datetime="{{ optional($story->created_at)->toDateString() }}" class="text-slate-500">{{ BanglaFormatter::shortDate($story->created_at) }}</time>
                                    @endif
                                </div>
                                <h3 class="mt-3 text-lg font-semibold text-slate-900">
                                    <a href="{{ route('posts.show', $story) }}" class="hover:text-indigo-600">{{ $story->title }}</a>
                                </h3>
                                <p class="mt-2 text-sm text-slate-600 flex-1">{{ $story->excerpt }}</p>
                                <div class="mt-4 flex items-center justify-between text-xs text-slate-500">
                                    @if($story->author)
                                        <span>রিপোর্ট: {{ $story->author->name }}</span>
                                    @endif
                                    <a href="{{ route('posts.show', $story) }}" class="font-semibold text-indigo-600 hover:text-indigo-700">বিস্তারিত</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($categorySections->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
            @foreach($categorySections as $category)
                <div>
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-slate-900">{{ $category->name }}</h2>
                        <a href="{{ route('home', ['category' => $category->slug]) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">আরও দেখুন</a>
                    </div>
                    <div class="mt-6 grid gap-6 lg:grid-cols-[2fr,3fr]">
                        @php
                            $categoryLead = $category->posts->first();
                        @endphp
                        <div>
                            @if($categoryLead)
                                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                                    @if($categoryLead->thumbnail_url)
                                        <a href="{{ route('posts.show', $categoryLead) }}" class="block">
                                            <img src="{{ $categoryLead->thumbnail_url }}" alt="{{ $categoryLead->title }}" class="aspect-[16/9] w-full object-cover">
                                        </a>
                                    @endif
                                    <div class="p-6">
                                        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-indigo-600">
                                            <span>{{ $category->name }}</span>
                                            @if($categoryLead->created_at)
                                                <span class="text-slate-400">•</span>
                                                <time datetime="{{ optional($categoryLead->created_at)->toDateString() }}" class="text-slate-500">{{ BanglaFormatter::shortDate($categoryLead->created_at) }}</time>
                                            @endif
                                        </div>
                                        <h3 class="mt-4 text-xl font-semibold text-slate-900">
                                            <a href="{{ route('posts.show', $categoryLead) }}" class="hover:text-indigo-600">{{ $categoryLead->title }}</a>
                                        </h3>
                                        <p class="mt-3 text-sm text-slate-600">{{ $categoryLead->excerpt }}</p>
                                        <a href="{{ route('posts.show', $categoryLead) }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                                            বিস্তারিত <i class="fas fa-arrow-right text-xs"></i>
                                        </a>
                                    </div>
                                </article>
                            @else
                                <div class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center text-slate-500">
                                    এই বিভাগে বর্তমানে কোনো সংবাদ নেই।
                                </div>
                            @endif
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach($category->posts->skip(1) as $post)
                                <article class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-indigo-200">
                                    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-indigo-600">
                                        <span>{{ $category->name }}</span>
                                        @if($post->created_at)
                                            <span class="text-slate-400">•</span>
                                            <time datetime="{{ optional($post->created_at)->toDateString() }}" class="text-slate-500">{{ BanglaFormatter::shortDate($post->created_at) }}</time>
                                        @endif
                                    </div>
                                    <h4 class="text-base font-semibold text-slate-900">
                                        <a href="{{ route('posts.show', $post) }}" class="hover:text-indigo-600">{{ $post->title }}</a>
                                    </h4>
                                    <p class="text-sm text-slate-600 line-clamp-3">{{ $post->excerpt }}</p>
                                    <a href="{{ route('posts.show', $post) }}" class="mt-auto text-sm font-semibold text-indigo-600 hover:text-indigo-700">বিস্তারিত</a>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </section>
    @endif

    <section class="bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">সর্বশেষ সব খবর</h2>
                    @if($search !== '' || $activeCategory)
                        <p class="mt-1 text-sm text-slate-600">
                            @if($search !== '' && $activeCategory)
                                “{{ $search }}” অনুসন্ধান · {{ $activeCategory->name }} বিভাগ
                            @elseif($search !== '')
                                “{{ $search }}” অনুসন্ধানের ফলাফল
                            @elseif($activeCategory)
                                {{ $activeCategory->name }} বিভাগের সব খবর
                            @endif
                        </p>
                    @endif
                </div>
                @if($search !== '' || $activeCategory)
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:border-indigo-500 hover:text-indigo-600">
                        <i class="fas fa-times text-xs"></i> ফিল্টার মুছুন
                    </a>
                @endif
            </div>
            <div class="mt-8 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @forelse($latestPosts as $post)
                    <article class="group flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        @if($post->thumbnail_url)
                            <a href="{{ route('posts.show', $post) }}" class="relative block aspect-[16/9] overflow-hidden bg-slate-100">
                                <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                @if($post->isVideo())
                                    <span class="absolute bottom-3 left-3 inline-flex items-center gap-1 rounded-full bg-black/70 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white">
                                        <i class="fas fa-play text-[10px]"></i>
                                        ভিডিও
                                        @if($post->video_duration)
                                            <span class="ml-2 text-[10px] font-medium normal-case tracking-normal">{{ $post->video_duration }}</span>
                                        @endif
                                    </span>
                                @endif
                            </a>
                        @endif
                        <div class="flex flex-1 flex-col p-6">
                            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wide text-indigo-600">
                                @if($post->category)
                                    <a href="{{ route('home', ['category' => $post->category->slug]) }}" class="hover:text-indigo-700">{{ $post->category->name }}</a>
                                @endif
                                @if($post->created_at)
                                    <span class="text-slate-400">•</span>
                                    <time datetime="{{ optional($post->created_at)->toDateString() }}" class="text-slate-500">{{ BanglaFormatter::shortDate($post->created_at) }}</time>
                                @endif
                            </div>
                            <h3 class="mt-3 text-xl font-semibold text-slate-900 group-hover:text-indigo-600">
                                <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
                            </h3>
                            <p class="mt-3 text-sm text-slate-600 flex-1">{{ $post->excerpt }}</p>
                            <div class="mt-6 flex items-center justify-between text-sm text-indigo-600">
                                <a href="{{ route('posts.show', $post) }}" class="font-semibold hover:text-indigo-700">
                                    {{ $post->isVideo() ? 'ভিডিও দেখুন' : 'খবরটি পড়ুন' }}
                                </a>
                                @if($post->author)
                                    <span class="text-slate-500">রিপোর্ট: {{ $post->author->name }}</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center text-slate-500">
                        কোনো খবর পাওয়া যায়নি। অনুগ্রহ করে ভিন্ন কীওয়ার্ড ব্যবহার করে আবার চেষ্টা করুন।
                    </div>
                @endforelse
            </div>
            <div class="mt-10">
                {{ $latestPosts->links() }}
            </div>
        </div>
    </section>
@endsection

@push('meta')
    @php
        $webSiteSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $settings->site_title ?? config('app.name'),
            'url' => $seo['canonical'] ?? url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => route('home', ['search' => '{search_term_string}']),
                'query-input' => 'required name=search_term_string',
            ],
        ];

        $collectionSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $seo['title'] ?? ($settings->site_title ?? config('app.name')),
            'description' => $seo['description'] ?? ($settings->site_meta_description ?? ''),
            'isPartOf' => $settings->site_title ?? config('app.name'),
            'url' => $seo['canonical'] ?? url('/'),
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($webSiteSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($collectionSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endpush
