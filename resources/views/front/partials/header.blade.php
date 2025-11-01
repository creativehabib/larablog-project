@php
    $activeCategory = $activeCategory ?? null;
    $isHomeRoute = request()->routeIs('home');
@endphp
<div class="hidden-xs hidden-print">
    <div class="container bgWhite">
        <div class="row">
            <div class="col-md-5">
                <div class="marginT8">
                    <span class="title1_6">ঢাকা, বাংলাদেশ</span>
                    <i class="fa fa-grip-lines-vertical title1_4 fontNormal marginLR3"></i>
                    <span class="title1_6">{{ $currentDateBangla ?? now()->format('d F Y') }}</span>
                    @if(!empty($currentTimeBangla))
                        <i class="fa fa-grip-lines-vertical title1_4 fontNormal marginLR3"></i>
                        <span class="title1_6"><strong>{{ $currentTimeBangla }}</strong></span>
                    @endif
                </div>
            </div>
            <div class="col-md-7">
                <div class="desktopHeaderLogoRightDiv">
                    <a aria-label="মতামত জরিপ" href="{{ route('polls.index') }}"><i class="fa fa-poll"></i> মতামত জরিপ</a>
                    <a aria-label="আরএসএস" href="{{ route('feed') }}"><i class="fa fa-rss"></i> আরএসএস</a>
                    <a aria-label="সাইটম্যাপ" href="{{ route('sitemap') }}"><i class="fa fa-sitemap"></i> সাইটম্যাপ</a>
                    <a aria-label="যোগাযোগ" href="mailto:{{ $settings->site_email ?? '' }}"><i class="fa fa-envelope"></i> যোগাযোগ</a>
                </div>
            </div>
        </div>
    </div>

    <nav id="navbar_top" class="navbar navbar-expand-lg borderC2T1">
        <div class="container">
            <div class="row">
                <div class="col-md-11">
                    <div class="headerMenu">
                        <span class="stickyLogo marginR20">
                            <a aria-label="Logo" href="{{ route('home') }}">
                                @if(!empty($logoUrl))
                                    <img src="{{ $logoUrl }}" alt="{{ $settings->site_title ?? config('app.name') }}" height="50">
                                @else
                                    <span class="title2_0 colorBlack">{{ $settings->site_title ?? config('app.name') }}</span>
                                @endif
                            </a>
                        </span>
                        <ul class="headerMenuUl">
                            <li class="{{ $isHomeRoute ? 'active' : '' }}"><a aria-label="প্রচ্ছদ" href="{{ route('home') }}">প্রচ্ছদ</a></li>
                            @foreach($navCategories ?? [] as $category)
                                <li class="{{ optional($activeCategory)->id === $category->id ? 'active' : '' }}">
                                    <a aria-label="{{ $category->name }}" href="{{ route('categories.show', $category) }}">{{ $category->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="text-right marginT10">
                        <a aria-label="Search" href="#" class="desktopSearchIcon colorBlack" id="desktopSearchToggle"><i class="fa fa-search"></i></a>
                        <span class="desktopSearchIcon marginL10 cursorPointer allMenu" id="desktopMenuToggle"><i class="fa fa-bars w17"></i></span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="desktopSearchForm" id="desktopSearchForm" style="display: none;">
                        <form action="{{ route('home') }}" method="get" class="desktopSearchFormInner">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="এখানে খুঁজুন...">
                                <span class="input-group-btn">
                                    <button class="btn btn-danger" type="submit"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="megaMenu megaMenuCustom" id="desktopMegaMenu" style="display: none;">
            <div class="bgWhite">
                <div class="container paddingT40 paddingB40">
                    <div class="col-md-12 paddingLR0">
                        <div class="desktopMegamenuSpecialLinks marginT10">
                            <a aria-label="সব সংবাদ" href="{{ route('home') }}"><i class="fa fa-table-list fontSize14"></i> সব সংবাদ</a>
                            <a aria-label="সর্বশেষ" href="{{ route('home') }}"><i class="fa fa-clock fontSize14"></i> সর্বশেষ</a>
                            <a aria-label="ভিডিও" href="{{ route('home') }}#video-section"><i class="fa fa-film fontSize14"></i> ভিডিও</a>
                            <a aria-label="পোল" href="{{ route('polls.index') }}"><i class="fa fa-square-poll-horizontal fontSize14"></i> পোল</a>
                            <a aria-label="আরএসএস" href="{{ route('feed') }}"><i class="fa fa-rss fontSize14"></i> আরএসএস</a>
                        </div>
                    </div>
                    <div class="col-md-12 paddingLR0"><p class="desktopDivider"></p></div>
                    <div class="col-md-12 paddingLR0">
                        <div class="row">
                            @foreach(($allCategories ?? collect())->chunk(6) as $chunk)
                                <div class="col-md-2 paddingL0">
                                    @foreach($chunk as $category)
                                        <div class="paddingT5 paddingB5">
                                            <a class="title11 textDecorationNone colorBlack hoverBlue" aria-label="{{ $category->name }}" href="{{ route('categories.show', $category) }}">{{ $category->name }}</a>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </nav>
</div>

<div class="visible-xs hidden-print">
    <div class="container borderC1B1">
        <div class="col-xs-8">
            <div class="marginT10">
                <a aria-label="Logo" href="{{ route('home') }}">
                    @if(!empty($logoUrl))
                        <img src="{{ $logoUrl }}" height="45" alt="{{ $settings->site_title ?? config('app.name') }}">
                    @else
                        <span class="title2_0 colorBlack">{{ $settings->site_title ?? config('app.name') }}</span>
                    @endif
                </a>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="text-right marginT7">
                <a aria-label="Search" href="#" id="mobileSearchToggle"><span><i class="fa fa-magnifying-glass searchIcon"></i></span></a>
                <span onclick="openNav()"><i class="fa fa-bars sidebarIcon"></i></span>
            </div>
        </div>
        <div class="col-xs-12 padding0 margin0 mobileSecondHeader" id="mobileSearchForm" style="display: none;">
            <div class="padding10">
                <form method="get" action="{{ route('home') }}">
                    <div class="row">
                        <div class="col-xs-10 paddingLR5">
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="এখানে খুঁজুন...">
                        </div>
                        <div class="col-xs-2 paddingLR5">
                            <button type="submit" class="btn btn-danger"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-xs-12 padding0 margin0 mobileSecondHeader">
            <div>
                <table>
                    <tr>
                        <td class="{{ $isHomeRoute ? 'mobileSecondHeaderActive' : '' }}"><a href="{{ route('home') }}"><i class="fa fa-house"></i></a></td>
                        @foreach(($navCategories ?? collect())->take(5) as $category)
                            <td class="{{ optional($activeCategory)->id === $category->id ? 'mobileSecondHeaderActive' : '' }}">
                                <a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a>
                            </td>
                        @endforeach
                        <td class="clickLoadMenubarCategories"><a href="javascript:void(0)"><i class="fa fa-ellipsis"></i></a></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-xs-12 padding0" id="mobileCategoryDrawer" style="display: none;">
            <div class="row loadMenubarCategories">
                @foreach(($allCategories ?? collect()) as $category)
                    <div class="col-xs-6">
                        <a class="sidebarCatTitle" aria-label="{{ $category->name }}" href="{{ route('categories.show', $category) }}"> {{ $category->name }}</a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
