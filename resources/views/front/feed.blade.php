@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ $settings->site_title ?? config('app.name') }}</title>
        <link>{{ route('home') }}</link>
        <description>{{ $settings->site_meta_description ?? $settings->site_description ?? config('app.name') . ' latest articles' }}</description>
        <language>{{ str_replace('_', '-', app()->getLocale()) }}</language>
        <lastBuildDate>{{ optional($updated)->toRfc2822String() }}</lastBuildDate>
        <atom:link href="{{ route('feed') }}" rel="self" type="application/rss+xml" />
        @foreach($posts as $post)
            <item>
                <title><![CDATA[{{ $post->title }}]]></title>
                <link>{{ route('posts.show', $post) }}</link>
                <guid isPermaLink="true">{{ route('posts.show', $post) }}</guid>
                <pubDate>{{ optional($post->created_at)->toRfc2822String() }}</pubDate>
                <description><![CDATA[{{ $post->meta_description ?? $post->excerpt }}]]></description>
                @if($post->category)
                    <category><![CDATA[{{ $post->category->name }}]]></category>
                @endif
            </item>
        @endforeach
    </channel>
</rss>
