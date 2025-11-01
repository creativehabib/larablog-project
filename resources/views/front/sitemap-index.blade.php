@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
@php echo '<?xml-stylesheet type="text/xsl" href="'.asset('xsl/sitemap-index.xsl').'"?>'; @endphp
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>{{ route('sitemap.pages') }}</loc>
        <lastmod>{{ now()->tz('UTC')->toAtomString() }}</lastmod>
    </sitemap>

    @foreach($postGroups as $group)
        @php
            $totalPages = $group->pages ?? 1;
            $baseUrl = route('sitemap.posts', [
                'year' => $group->year,
                'month' => str_pad($group->month, 2, '0', STR_PAD_LEFT)
            ]);
        @endphp

        @for($page = 1; $page <= $totalPages; $page++)
            @php
                $pageUrl = $baseUrl . ($page > 1 ? '?page=' . $page : '');
            @endphp
            <sitemap>
                <loc>{{ $pageUrl }}</loc>
                <lastmod>
                    @if($group->lastmod)
                        {{ \Carbon\Carbon::parse($group->lastmod)->tz('UTC')->toAtomString() }}
                    @endif
                </lastmod>
            </sitemap>
        @endfor
    @endforeach

    <sitemap>
        <loc>{{ route('sitemap.categories') }}</loc>
        @if($categoryLastUpdated)
            <lastmod>
                {{ \Carbon\Carbon::parse($categoryLastUpdated)->tz('UTC')->toAtomString() }}
            </lastmod>
        @endif
    </sitemap>
</sitemapindex>
