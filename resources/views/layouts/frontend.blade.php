<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $seo['title'] ?? ($settings->site_title ?? config('app.name')) }}</title>
    <!-- FAVICONS -->
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('assets/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/favicon.ico') }}">
    <meta name="theme-color" content="#3063A0"><!-- End FAVICONS -->
    <meta name="description" content="{{ $seo['description'] ?? ($settings->site_meta_description ?? '') }}">
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

    <meta name="twitter:card" content="{{ $seo['twitter_card'] ?? (!empty($seo['image']) ? 'summary_large_image' : 'summary') }}">
    <meta name="twitter:title" content="{{ $seo['title'] ?? '' }}">
    <meta name="twitter:description" content="{{ $seo['description'] ?? '' }}">
    @if(!empty($seo['image']))
        <meta name="twitter:image" content="{{ $seo['image'] }}">
    @endif

    <link rel="alternate" type="application/rss+xml" title="{{ $settings->site_title ?? config('app.name') }} RSS" href="{{ route('feed') }}">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Calibri">
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendors/bootstrap3.7/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendors/datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendors/fontawesome6/css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendors/fontawesome6/css/brands.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendors/fontawesome6/css/solid.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendors/flex-gallery/flexslider.css') }}" media="screen">
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendors/custom/custom.css') }}">
    @stack('meta')
    @stack('styles')
</head>
<body class="bgWhite">
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
                            <li class="{{ request('category') ? '' : 'active' }}"><a aria-label="প্রচ্ছদ" href="{{ route('home') }}">প্রচ্ছদ</a></li>
                            @foreach($navCategories ?? [] as $category)
                                <li class="{{ optional($activeCategory)->id === $category->id ? 'active' : '' }}">
                                    <a aria-label="{{ $category->name }}" href="{{ route('home', ['category' => $category->slug]) }}">{{ $category->name }}</a>
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
                                            <a class="title11 textDecorationNone colorBlack hoverBlue" aria-label="{{ $category->name }}" href="{{ route('home', ['category' => $category->slug]) }}">{{ $category->name }}</a>
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
                        <td class="{{ request('category') ? '' : 'mobileSecondHeaderActive' }}"><a href="{{ route('home') }}"><i class="fa fa-house"></i></a></td>
                        @foreach(($navCategories ?? collect())->take(5) as $category)
                            <td class="{{ optional($activeCategory)->id === $category->id ? 'mobileSecondHeaderActive' : '' }}">
                                <a href="{{ route('home', ['category' => $category->slug]) }}">{{ $category->name }}</a>
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
                        <a class="sidebarCatTitle" aria-label="{{ $category->name }}" href="{{ route('home', ['category' => $category->slug]) }}"> {{ $category->name }}</a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

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
                    <div class="col-xs-6"><a class="sidebarCatTitle" aria-label="{{ $category->name }}" href="{{ route('home', ['category' => $category->slug]) }}"> {{ $category->name }}</a></div>
                @endforeach
                <div class="col-xs-6"><a class="sidebarCatTitle" aria-label="মতামত জরিপ" href="{{ route('polls.index') }}"><i class="fa fa-poll"></i> মতামত জরিপ</a></div>
                <div class="col-xs-6"><a class="sidebarCatTitle" aria-label="আরএসএস" href="{{ route('feed') }}"><i class="fa fa-rss"></i> আরএসএস</a></div>
            </div>
        </div>
    </div>
</div>

<div class="mainDiv">
    @yield('content')
</div>

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

<div class="container marginT50 visible-xs mobileFooter hidden-print">
    <div class="row">
        <div class="col-xs-12 paddingL5">
            <div class="editorDiv text-center">
                <h4 class="title2 margin5">{{ $settings->site_title ?? config('app.name') }}</h4>
                @if(!empty($settings?->site_description))
                    <p class="margin5">{{ $settings->site_description }}</p>
                @endif
            </div>
            <div class="text-center followDiv hidden-print">
                <p class="marginB0">সঙ্গে থাকুন</p>
                @if(!empty($settings?->site_email))
                    <p class="marginB0">ই-মেইল: {{ $settings->site_email }}</p>
                @endif
                @if(!empty($settings?->site_phone))
                    <p class="marginB0">ফোন: {{ $settings->site_phone }}</p>
                @endif
                <p class="copyright"><span class="todaysYear"></span> <i class="fa fa-copyright"></i> {{ $settings->site_title ?? config('app.name') }}</p>
            </div>
            <div class="linkDiv hidden-print">
                <a aria-label="পোল" href="{{ route('polls.index') }}">অনলাইন জরিপ</a>
                <a aria-label="আরএসএস" href="{{ route('feed') }}">আরএসএস</a>
                <a aria-label="সাইটম্যাপ" href="{{ route('sitemap') }}">সাইটম্যাপ</a>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('frontend/assets/vendors/jquery/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('frontend/assets/vendors/bootstrap3.7/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('frontend/assets/vendors/datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('frontend/assets/vendors/flex-gallery/jquery.flexslider.js') }}" defer></script>
<script src="{{ asset('frontend/assets/vendors/loadscroll/jQuery.loadScroll.js') }}"></script>
<script>
    function openNav() {
        document.getElementById('mySidepanel').style.width = '100%';
    }

    function closeNav() {
        document.getElementById('mySidepanel').style.width = '0';
    }

    $(function () {
        $('#desktopSearchToggle').on('click', function (e) {
            e.preventDefault();
            $('#desktopSearchForm').slideToggle(200);
        });

        $('#desktopMenuToggle').on('click', function () {
            $('#desktopMegaMenu').slideToggle(200);
        });

        $('#mobileSearchToggle').on('click', function (e) {
            e.preventDefault();
            $('#mobileSearchForm').slideToggle(200);
        });

        $('.clickLoadMenubarCategories').on('click', function () {
            $('#mobileCategoryDrawer').slideToggle(200);
        });

        $('.todaysYear').text(new Date().getFullYear());
    });
</script>
<!-- header fixed -->
<script type="text/javascript">
    var width = $(window).width();
    if(width >= 768){
        document.addEventListener("DOMContentLoaded", function(){
            window.addEventListener('scroll', function() {
                if (window.scrollY > 45) {
                    document.getElementById('navbar_top').classList.add('fixed-top');
                    navbar_height = document.querySelector('.navbar').offsetHeight;
                    document.body.style.paddingTop = navbar_height + 'px';
                } else {
                    document.getElementById('navbar_top').classList.remove('fixed-top');
                    document.body.style.paddingTop = '0';
                }
            });
        });
    }
</script>


<!-- device wise div remove -->
<script type="text/javascript">
    var width = $(window).width();
    if(width >= 768){
        $('.visible-xs').remove();
    }
    if(width <= 767){
        $('.hidden-xs').remove();
    }
</script>



<!-- sidebar desktop -->
<script type="text/javascript">
    function openNavDesktop() {
        document.getElementById("desktopSidebar").style.width = "300px";
    }
    function closeNavDesktop() {
        document.getElementById("desktopSidebar").style.width = "0";
    }
</script>

<!-- sidebar mobile -->
<script type="text/javascript">
    function openNav() {
        document.getElementById("mySidepanel").style.width = "100%";
    }
    function closeNav() {
        document.getElementById("mySidepanel").style.width = "0";
    }
</script>

<!-- go to top -->
<script type="text/javascript">
    $(window).scroll(function () {
        if ($(window).scrollTop() > 300) {
            $('.go-to-top').show();
        } else {
            $('.go-to-top').hide();
        }
    });
    function gotop() {
        var scrollStep = -window.scrollY / 300,
            scrollInterval = setInterval(function () {
                if (window.scrollY != 0) {
                    window.scrollBy(0, scrollStep);
                } else clearInterval(scrollInterval);
            }, 2);
    }
</script>

<!-- poll -->
<script type="text/javascript">
    jQuery(document).ready(function(){
        $(".clickVote").on("change", function(){
            var voteType = $(this).data('votetype');
            var pollId = $(this).data('pollid');
            $.ajax({
                type: 'GET',
                url: 'https://larasblog.test/poll/store'+"/"+pollId+"/"+voteType,
                success: function (data) {
                    if(data != ''){
                        $('.clickVoteInput'+pollId).attr('disabled', true);
                        $('.totalyesVote'+pollId).html(data.yes_vote_percent_bangla+' %');
                        $('.totalNoVote'+pollId).html(data.no_vote_percent_bangla+' %');
                        $('.totalNoCommentVote'+pollId).html(data.no_opinion_vote_percent_bangla+' %');
                        $('.totalVoter'+pollId).html(data.total_vote_bangla);

                        $('.totalyesVote'+pollId).show();
                        $('.totalNoVote'+pollId).show();
                        $('.totalNoCommentVote'+pollId).show();
                    }
                }
            });
        });
    });
</script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script type="text/javascript">
    $(".downloadPoll").on('click', function () {
        var pollId = $(this).data('pollid');
        $('.downloadPoll').hide();
        $('.downloadPollShareIcon').hide();
        $('.pollDownloadTime').show();
        var pollDate = $(this).data('polldate');
        html2canvas(document.getElementById("pollContentDiv"+pollId)).then(function (canvas) {
            var anchorTag = document.createElement("a");
            document.body.appendChild(anchorTag);
            anchorTag.download = pollDate+".png";
            anchorTag.href = canvas.toDataURL();
            anchorTag.target = '_blank';
            anchorTag.click();
            $('.downloadPoll').show();
            $('.downloadPollShareIcon').show();
            $('.pollDownloadTime').hide();
        });
    });
</script>

<!-- jquery onscroll image loader -->
<script type="text/javascript">
    $(document).ready(function () {
        $('img').loadScroll();
    });
</script>


<!-- FlexSlider -->
<script type="text/javascript">
    $(function(){
        // SyntaxHighlighter.all();
    });
    $(window).on('load', function(){
        $('.flexslider').flexslider({
            controlNav: false,
            animation: "slide",
            animationLoop: false,
            directionNav: true,
            itemWidth: 260,
            itemMargin: 20,
            pausePlay: true,
            nextText: "",
            prevText: "",
            start: function(slider){
                $('body').removeClass('loading');
            }
        });
    });
</script>
@stack('scripts')
</body>
</html>
