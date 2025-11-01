@extends('layouts.front')

@section('content')
<section class="bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-3xl">
            <h1 class="text-3xl font-bold text-slate-900 sm:text-4xl">
                {{ $seo['title'] ?? ($settings->site_title ?? config('app.name')) }}
            </h1>
            <p class="mt-4 text-lg text-slate-600">
                {{ $seo['description'] ?? ($settings->site_meta_description ?? 'Stay informed with the latest stories and insights.') }}
            </p>
            @if(!empty($search))
                <p class="mt-4 text-sm text-slate-500">Showing results for “{{ $search }}”. <a class="text-indigo-600 hover:text-indigo-700" href="{{ route('home') }}">Clear search</a></p>
            @endif
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($posts as $post)
            <article class="group flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                @if ($post->thumbnail_url)
                    <a href="{{ post_permalink($post) }}" class="relative block aspect-[16/9] overflow-hidden bg-slate-100">
                        <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @if($post->isVideo())
                            <span class="absolute bottom-3 left-3 inline-flex items-center gap-1 rounded-full bg-black/70 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white">
                                <i class="fas fa-play text-[10px]"></i>
                                Video
                                @if($post->video_duration)
                                    <span class="ml-2 text-[10px] font-medium normal-case tracking-normal">{{ $post->video_duration }}</span>
                                @endif
                            </span>
                        @endif
                    </a>
                @endif
                <div class="flex flex-1 flex-col p-6">
                    <div class="flex items-center gap-2 text-xs uppercase tracking-wide text-indigo-600">
                        @if($post->category)
                            <span>{{ $post->category->name }}</span>
                        @endif
                        <span class="text-slate-400">•</span>
                        <time datetime="{{ optional($post->created_at)->toDateString() }}" class="text-slate-500">{{ optional($post->created_at)->format('M d, Y') }}</time>
                        @if($post->isVideo())
                            <span class="text-slate-400">•</span>
                            <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 font-semibold text-indigo-600">Video</span>
                            @if($post->video_duration)
                                <span class="text-slate-400">•</span>
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">{{ $post->video_duration }}</span>
                            @endif
                            @if($post->playlist)
                                <span class="text-slate-400">•</span>
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600">{{ $post->playlist->name }}</span>
                            @endif
                        @endif
                    </div>
                    <h2 class="mt-3 text-xl font-semibold text-slate-900 group-hover:text-indigo-600">
                        <a href="{{ post_permalink($post) }}">{{ $post->title }}</a>
                    </h2>
                    <p class="mt-3 text-sm text-slate-600 flex-1">{{ $post->excerpt }}</p>
                    <div class="mt-6 flex items-center justify-between text-sm text-indigo-600">
                        <a href="{{ post_permalink($post) }}" class="font-semibold hover:text-indigo-700">
                            {{ $post->isVideo() ? 'Watch video' : 'Read full story' }}
                        </a>
                        @if($post->author)
                            <span class="text-slate-500">By {{ $post->author->name }}</span>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full text-center">
                <p class="text-lg font-medium text-slate-600">No articles found.</p>
                <p class="mt-2 text-sm text-slate-500">Try adjusting your search or check back later for new stories.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $posts->links() }}
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
        ];

        $webSiteSchema['potentialAction'] = [
            '@type' => 'SearchAction',
            'target' => route('home') . '?search={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($webSiteSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
