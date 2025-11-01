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

@include('front.partials.header')

<div class="mainDiv">
    @yield('content')
</div>

@include('front.partials.footer')

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
