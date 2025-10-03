<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $seo['title'] ?? ($settings->site_title ?? config('app.name')) }}</title>
    <meta name="description" content="{{ $seo['description'] ?? '' }}">
    @if(!empty($seo['keywords']))
        <meta name="keywords" content="{{ $seo['keywords'] }}">
    @endif
    <meta name="robots" content="{{ ($seo['indexable'] ?? true) ? 'index,follow' : 'noindex,nofollow' }}">
    <link rel="canonical" href="{{ $seo['canonical'] ?? url()->current() }}">

    <meta property="og:type" content="{{ $seo['type'] ?? 'website' }}">
    <meta property="og:title" content="{{ $seo['title'] ?? '' }}">
    <meta property="og:description" content="{{ $seo['description'] ?? '' }}">
    <meta property="og:url" content="{{ $seo['canonical'] ?? url()->current() }}">
    <meta property="og:site_name" content="{{ $settings->site_title ?? config('app.name') }}">
    @if(!empty($seo['image']))
        <meta property="og:image" content="{{ $seo['image'] }}">
    @endif
    @if(!empty($seo['published_time']))
        <meta property="article:published_time" content="{{ $seo['published_time'] }}">
    @endif
    @if(!empty($seo['modified_time']))
        <meta property="article:modified_time" content="{{ $seo['modified_time'] }}">
    @endif

    <meta name="twitter:card" content="{{ !empty($seo['image']) ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $seo['title'] ?? '' }}">
    <meta name="twitter:description" content="{{ $seo['description'] ?? '' }}">
    @if(!empty($seo['image']))
        <meta name="twitter:image" content="{{ $seo['image'] }}">
    @endif

    <link rel="alternate" type="application/rss+xml" title="{{ $settings->site_title ?? config('app.name') }} RSS Feed" href="{{ route('feed') }}">

    @stack('meta')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-900">
<a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 bg-slate-900 text-white px-4 py-2 rounded-md">Skip to content</a>
<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('home') }}" class="text-2xl font-bold text-slate-900 hover:text-indigo-600 transition-colors">
                {{ $settings->site_title ?? config('app.name') }}
            </a>
            @if(!empty($settings?->site_description))
                <p class="mt-1 text-sm text-slate-500">{{ $settings->site_description }}</p>
            @endif
        </div>
        <form action="{{ route('home') }}" method="get" class="w-full md:w-auto md:min-w-[320px]">
            <label for="search" class="sr-only">Search</label>
            <div class="relative">
                <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Search articles..." class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-sm focus:border-indigo-500 focus:bg-white focus:outline-none">
                <button type="submit" class="absolute inset-y-0 right-1 my-1 px-3 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Search</button>
            </div>
        </form>
    </div>
</header>
<main id="main" class="min-h-screen">
    @yield('content')
</main>
<footer class="bg-slate-900 text-slate-200 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid gap-8 md:grid-cols-3">
        <div>
            <h2 class="text-lg font-semibold">About</h2>
            <p class="mt-2 text-sm leading-relaxed text-slate-400">
                {{ $settings?->site_meta_description ?? 'Latest stories, updates, and breaking news from our editorial desk.' }}
            </p>
        </div>
        <div>
            <h2 class="text-lg font-semibold">Contact</h2>
            <ul class="mt-2 space-y-1 text-sm text-slate-400">
                @if(!empty($settings?->site_email))
                    <li>Email: <a href="mailto:{{ $settings->site_email }}" class="text-slate-200 hover:text-white">{{ $settings->site_email }}</a></li>
                @endif
                @if(!empty($settings?->site_phone))
                    <li>Phone: <a href="tel:{{ preg_replace('/[^\d+]/', '', $settings->site_phone) }}" class="text-slate-200 hover:text-white">{{ $settings->site_phone }}</a></li>
                @endif
            </ul>
        </div>
        <div>
            <h2 class="text-lg font-semibold">Stay Updated</h2>
            <p class="mt-2 text-sm text-slate-400">Subscribe to our RSS feed for instant updates.</p>
            <a href="{{ route('feed') }}" class="mt-3 inline-flex items-center rounded-md bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-600">View RSS Feed</a>
        </div>
    </div>
    <div class="border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-slate-500 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <span>&copy; {{ now()->year }} {{ $settings->site_title ?? config('app.name') }}. All rights reserved.</span>
            @if(!empty($settings?->site_copyright))
                <span>{{ $settings->site_copyright }}</span>
            @endif
        </div>
    </div>
</footer>
@stack('scripts')
</body>
</html>
