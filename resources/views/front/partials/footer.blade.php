<footer class="desktopFooter hidden-xs hidden-print">
    <div class="container marginT50">
        <div class="row desktopFlexRow">
            <div class="col-sm-12 col-md-12">
                <div class="borderC1B1 paddingB20 text-center">
                    <a aria-label="Logo" href="{{ route('home') }}">
                        @if(!empty($logoUrl))
                            <img src="{{ $logoUrl }}" height="80" alt="{{ $settings->site_title ?? config('app.name') }}">
                        @else
                            <span class="title2_0 colorBlack">{{ $settings->site_title ?? config('app.name') }}</span>
                        @endif
                    </a>
                </div>
            </div>
            <div class="col-sm-4 col-md-4">
                <div class="editorDiv paddingT30 h100P">
                    <h4 class="title11">{{ $settings->site_title ?? config('app.name') }}</h4>
                    @if(!empty($settings?->site_description))
                        <p class="desktopSummary marginT10">{{ $settings->site_description }}</p>
                    @endif
                </div>
            </div>
            <div class="col-sm-4 col-md-4">
                <div class="linkDiv paddingT30 h100P">
                    @if(!empty($settings?->site_email))
                        <a class="hoverBlue" aria-label="Email" href="mailto:{{ $settings->site_email }}">ই-মেইল: {{ $settings->site_email }}</a>
                    @endif
                    @if(!empty($settings?->site_phone))
                        <a class="hoverBlue" aria-label="Phone" href="tel:{{ preg_replace('/[^\d+]/', '', $settings->site_phone) }}">ফোন: {{ $settings->site_phone }}</a>
                    @endif
                    <a class="hoverBlue" aria-label="পোল" href="{{ route('polls.index') }}">অনলাইন জরিপ</a>
                    <a class="hoverBlue" aria-label="আরএসএস" href="{{ route('feed') }}">আরএসএস ফিড</a>
                </div>
            </div>
            <div class="col-sm-4 col-md-4">
                <div class="followDiv h100P text-center">
                    <p class="marginB0 title11">সাম্প্রতিক আপডেট</p>
                    <p class="marginB0 marginT10">{{ $seo['description'] ?? ($settings->site_meta_description ?? '') }}</p>
                </div>
            </div>
            @if(($footerMenu?->items ?? collect())->isNotEmpty())
                <div class="col-sm-12 col-md-12">
                    <div class="footerMenuWrapper marginT20">
                        <ul class="footerMenu">
                            @foreach($footerMenu->items as $item)
                                <li>
                                    <a href="{{ $item->url }}" target="{{ $item->target }}">{{ $item->title }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            <div class="col-sm-12 col-md-12">
                <div class="footerBottom">
                    <p><span class="todaysYear"></span> <i class="fa fa-copyright title1_4"></i> {{ $settings->site_title ?? config('app.name') }} কর্তৃক সর্বস্বত্ব সংরক্ষিত।</p>
                    @if(!empty($settings?->site_copyright))
                        <p>{{ $settings->site_copyright }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</footer>
