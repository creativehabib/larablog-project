@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
@php echo '<?xml-stylesheet type="text/xsl" href="'.asset('xsl/sitemap-urlset.xsl').'"?>'; @endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($categories as $category)
        <url>
            <loc>{{ route('categories.show', $category) }}</loc>
            <lastmod>{{ optional($category->updated_at ?? $category->created_at)->tz('UTC')->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach
</urlset>
