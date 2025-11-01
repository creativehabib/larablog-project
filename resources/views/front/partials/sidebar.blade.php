<div id="mySidepanel" class="sidepanel visible-xs hidden-print">
    <div class="container borderC1B1">
        <div class="col-xs-8">
            <div>
                <a style="border: none;" aria-label="Logo" href="{{ route('home') }}">
                    @if(!empty($logoUrl))
                        <img src="{{ $logoUrl }}" height="45" alt="{{ $settings->site_title ?? config('app.name') }}">
                    @else
                        <span class="title2_0 colorBlack">{{ $settings->site_title ?? config('app.name') }}</span>
                    @endif
                </a>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="text-right">
                <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="col-xs-12">
            <div class="borderC1-1 shadow1 borderRadius5 marginT15 marginB15 paddingLR20 paddingT10 paddingB10">
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

        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-6"><a class="sidebarCatTitle" aria-label="প্রচ্ছদ" href="{{ route('home') }}"> প্রচ্ছদ</a></div>
                @foreach($allCategories ?? [] as $category)
                    <div class="col-xs-6"><a class="sidebarCatTitle" aria-label="{{ $category->name }}" href="{{ route('categories.show', $category) }}"> {{ $category->name }}</a></div>
                @endforeach
                <div class="col-xs-6"><a class="sidebarCatTitle" aria-label="মতামত জরিপ" href="{{ route('polls.index') }}"><i class="fa fa-poll"></i> মতামত জরিপ</a></div>
                <div class="col-xs-6"><a class="sidebarCatTitle" aria-label="আরএসএস" href="{{ route('feed') }}"><i class="fa fa-rss"></i> আরএসএস</a></div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-3 col-md-3 hidden-xs hidden-print">
    <aside class="desktopSidebar" aria-label="Sidebar navigation">
        <div class="desktopSidebarCard borderC1-1 shadow1 borderRadius5 marginB20 paddingLR20 paddingT15 paddingB15">
            <form method="get" action="{{ route('home') }}">
                <label for="desktopSidebarSearch" class="sr-only">Search</label>
                <div class="input-group">
                    <input
                        id="desktopSidebarSearch"
                        type="text"
                        name="search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="এখানে খুঁজুন..."
                    >
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-danger"><i class="fa fa-search"></i></button>
                    </span>
                </div>
            </form>
        </div>

        @if(!empty($activePoll))
            <div class="desktopSidebarCard borderC1-1 borderRadius5 marginB20 paddingLR20 paddingT15 paddingB15">
                <h3 class="desktopSidebarCardHeading title11 marginT0 marginB10">মতামত জরিপ</h3>
                <p class="title1_8 marginB15">
                    {{ $activePoll->question }}
                </p>
                <a href="{{ route('polls.index') }}" class="btn btn-danger btn-block">ভোট দিন</a>
                @if(!empty($activePoll->poll_date_bangla))
                    <p class="marginT10 marginB0 time">{{ $activePoll->poll_date_bangla }} পর্যন্ত</p>
                @endif
            </div>
        @endif

        @if(($latestSidebarPosts ?? collect())->isNotEmpty())
            <div class="desktopSidebarCard borderC1-1 borderRadius5 marginB20 paddingLR20 paddingT15 paddingB15">
                <h3 class="desktopSidebarCardHeading title11 marginT0 marginB15">সর্বশেষ সংবাদ</h3>
                <ul class="desktopSidebarList">
                    @foreach(($latestSidebarPosts ?? collect())->take(6) as $post)
                        <li>
                            <a class="title12 textDecorationNone colorBlack hoverBlue" href="{{ route('posts.show', $post) }}">
                                {{ \Illuminate\Support\Str::limit($post->title, 90) }}
                            </a>
                            @if($post->category)
                                <span class="time">{{ $post->category->name }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(($popularPosts ?? collect())->isNotEmpty())
            <div class="desktopSidebarCard borderC1-1 borderRadius5 marginB20 paddingLR20 paddingT15 paddingB15">
                <h3 class="desktopSidebarCardHeading title11 marginT0 marginB15">জনপ্রিয় সংবাদ</h3>
                <ul class="desktopSidebarList">
                    @foreach(($popularPosts ?? collect())->take(6) as $post)
                        <li>
                            <a class="title12 textDecorationNone colorBlack hoverBlue" href="{{ route('posts.show', $post) }}">
                                {{ \Illuminate\Support\Str::limit($post->title, 90) }}
                            </a>
                            @if($post->category)
                                <span class="time">{{ $post->category->name }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $sidebarCategories = ($allCategories ?? collect())->take(12);
        @endphp
        @if($sidebarCategories->isNotEmpty())
            <div class="desktopSidebarCard borderC1-1 borderRadius5 marginB0 paddingLR20 paddingT15 paddingB15">
                <h3 class="desktopSidebarCardHeading title11 marginT0 marginB15">দ্রুত লিংক</h3>
                <ul class="desktopSidebarList">
                    <li>
                        <a class="sidebarCatTitle textDecorationNone colorBlack hoverBlue" aria-label="প্রচ্ছদ" href="{{ route('home') }}">প্রচ্ছদ</a>
                    </li>
                    @foreach($sidebarCategories as $category)
                        <li>
                            <a class="sidebarCatTitle textDecorationNone colorBlack hoverBlue" aria-label="{{ $category->name }}" href="{{ route('categories.show', $category) }}">{{ $category->name }}</a>
                        </li>
                    @endforeach
                    <li>
                        <a class="sidebarCatTitle textDecorationNone colorBlack hoverBlue" aria-label="মতামত জরিপ" href="{{ route('polls.index') }}">
                            <i class="fa fa-poll marginR5"></i> মতামত জরিপ
                        </a>
                    </li>
                    <li>
                        <a class="sidebarCatTitle textDecorationNone colorBlack hoverBlue" aria-label="আরএসএস" href="{{ route('feed') }}">
                            <i class="fa fa-rss marginR5"></i> আরএসএস
                        </a>
                    </li>
                </ul>
            </div>
        @endif
    </aside>
</div>
