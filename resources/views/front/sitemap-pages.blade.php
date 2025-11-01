@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
@php echo '<?xml-stylesheet type="text/xsl" href="'.asset('xsl/sitemap-urlset.xsl').'"?>'; @endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($pages as $page)
        <url>
            <loc>{{ $page['url'] }}</loc>
            <lastmod>{{ $page['lastmod']->tz('UTC')->toAtomString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.5</priority>
        </url>
    @endforeach
</urlset>
