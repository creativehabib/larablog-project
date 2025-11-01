@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
@php echo '<?xml-stylesheet type="text/xsl" href="'.asset('xsl/sitemap-urlset.xsl').'"?>'; @endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($posts as $post)
        <url>
            <loc>{{ post_permalink($post) }}</loc>
            <lastmod>{{ optional($post->updated_at ?? $post->created_at)->tz('UTC')->toAtomString() }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
</urlset>
