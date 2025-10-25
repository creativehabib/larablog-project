@extends('layouts.frontend')

@php
    use App\Support\BanglaFormatter;
@endphp

@section('content')
    @if($trendingTopics->isNotEmpty())
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-12">
                    <div class="marginT15 hidden-print">
                        <p>
                            <span class="desktopTrendingTopicItem bgRed colorWhite">ট্রেন্ডিং</span>
                            @foreach($trendingTopics as $topic)
                                <a class="desktopTrendingTopicItem" aria-label="{{ $topic->name }}" href="{{ route('home', ['category' => $topic->slug]) }}">{{ $topic->name }}</a>
                            @endforeach
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="container marginT10">
        <div class="row">
            <div class="col-sm-9 col-md-9">
                <div class="row marginLR-10">
                    <div class="col-sm-8 col-md-8 paddingLR10 desktopSectionLead">
                        @if($leadStory)
                            <div class="thumbnail">
                                <div class="positionRelative">
                                    @if($leadStory->thumbnail_url)
                                        <img src="{{ $leadStory->thumbnail_url }}" class="img-responsive borderRadius5" alt="{{ $leadStory->title }}">
                                    @else
                                        <img src="{{ asset('frontend/assets/images/sports-banner.jpg') }}" class="img-responsive borderRadius5" alt="{{ $leadStory->title }}">
                                    @endif
                                    @if($leadStory->isVideo())
                                        <span class="fa fa-play pvIconMedium"></span>
                                    @endif
                                </div>
                                <div class="caption">
                                    <h1 class="title9 marginT5 marginB5"><strong>{{ $leadStory->title }}</strong></h1>
                                    <p class="desktopSummary marginB5 title1_8">{{ $leadStory->excerpt }}</p>
                                </div>
                                <a href="{{ route('posts.show', $leadStory) }}" class="linkOverlay"></a>
                            </div>
                        @else
                            <div class="thumbnail borderC1B1">
                                <div class="caption">
                                    <p class="desktopSummary marginB5">বর্তমানে কোনো প্রধান সংবাদ পাওয়া যায়নি। অনুগ্রহ করে পরে চেষ্টা করুন।</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-sm-4 col-md-4 paddingLR10 topNews">
                        @if($topStories->isNotEmpty())
                            <div class="desktopSectionLead marginB15">
                                @php $topHighlight = $topStories->first(); @endphp
                                @if($topHighlight)
                                    <div class="thumbnail borderRadius0 bgUnset borderC1B1">
                                        <div class="positionRelative">
                                            @if($topHighlight->thumbnail_url)
                                                <img src="{{ $topHighlight->thumbnail_url }}" class="img-responsive borderRadius5" alt="{{ $topHighlight->title }}">
                                            @else
                                                <img src="{{ asset('frontend/assets/images/entertainment-banner4.jpeg') }}" class="img-responsive borderRadius5" alt="{{ $topHighlight->title }}">
                                            @endif
                                            @if($topHighlight->isVideo())
                                                <span class="fa fa-play pvIconMedium"></span>
                                            @endif
                                        </div>
                                        <div class="caption paddingTB0 paddingLR10">
                                            <h3 class="title11 marginT0"><strong>{{ $topHighlight->title }}</strong></h3>
                                        </div>
                                        <a href="{{ route('posts.show', $topHighlight) }}" class="linkOverlay"></a>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="desktopSectionListMedia">
                            @foreach($topStories->skip(1) as $story)
                                <div class="media positionRelative">
                                    <div class="media-body">
                                        <h3 class="title12">
                                            <strong>
                                                @if($story->category)
                                                    <span class="shoulder">{{ $story->category->name }}</span>
                                                    <i class="fa fa-circle dot"></i>
                                                @endif
                                                {{ $story->title }}
                                            </strong>
                                        </h3>
                                        <p class="desktopSummary marginT5 title1_6 marginB0 truncate-2-lines">
                                            {{ $story->excerpt }}
                                        </p>
                                    </div>
                                    <a href="{{ route('posts.show', $story) }}" class="linkOverlay"></a>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-12 paddingLR10"><p class="desktopDivider marginT10 marginB10"></p></div>
                </div>

                @if($moreStories->isNotEmpty())
                    <div class="row">
                            <div class="col-sm-12 col-md-12 marginT15">
                                <div class="row marginLR-10 desktopFlexRow">
                                    @foreach($moreStories as $story)
                                        <div class="col-sm-3 col-md-3 paddingLR10 desktopSectionLead">
                                            <div class="thumbnail marginB15">
                                                <div class="positionRelative">
                                                    @if($story->thumbnail_url)
                                                        <img src="{{ $story->thumbnail_url }}" class="img-responsive borderRadius5" alt="{{ $story->title }}">
                                                    @else
                                                        <img src="{{ asset('frontend/assets/images/sports-banner.jpg') }}" class="img-responsive borderRadius5" alt="{{ $story->title }}">
                                                    @endif
                                                    @if($story->isVideo())
                                                        <span class="fa fa-play pvIconMedium"></span>
                                                    @endif
                                                </div>
                                                <div class="caption">
                                                    <h3 class="title12 marginT0 truncate-2-lines"><strong>{{ $story->title }}</strong></h3>
                                                </div>
                                                <a href="{{ route('posts.show', $story) }}" class="linkOverlay"></a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <p class="desktopDivider marginT0 marginB10"></p>
                        </div>
                    </div>
                @endif

                @if($latestVideos->isNotEmpty())
                    <div class="marginT40" id="video-section">
                        <div class="row">
                            <div class="col-sm-12 col-md-12">
                                <h2 class="desktopSectionTitle">
                                    <a aria-label="ভিডিও" href="{{ route('home') }}#video-section">ভিডিও</a>
                                    <a href="{{ route('home') }}#video-section" class="float-right"><i class="fa fa-angle-double-right title1_6"></i></a>
                                    <span class="bottomMarked"></span>
                                </h2>
                            </div>
                        </div>
                        <div class="category1 slider customFlexSlider">
                            <div class="flexslider carousel">
                                <ul class="slides">
                                    @foreach($latestVideos as $video)
                                        <li>
                                            <div class="desktopSectionLead">
                                                <div class="thumbnail borderRadius0 bgUnset">
                                                    <a href="{{ route('posts.show', $video) }}">
                                                        <div class="positionRelative">
                                                            @if($video->thumbnail_url)
                                                                <img src="{{ $video->thumbnail_url }}" class="img-responsive borderRadius5" alt="{{ $video->title }}">
                                                            @else
                                                                <img src="{{ asset('frontend/assets/images/entertainment-banner4.jpeg') }}" class="img-responsive borderRadius5" alt="{{ $video->title }}">
                                                            @endif
                                                            <span class="fa fa-play pvIconMedium"></span>
                                                        </div>
                                                        <div class="caption paddingTB0 paddingLR10">
                                                            <h3 class="title12 marginT0 truncate-2-lines">{{ $video->title }}</h3>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row"><div class="col-sm-12 col-md-12"><p class="desktopDivider marginT0 marginB0"></p></div></div>
                @endif

                @if($categorySections->isNotEmpty())
                    @foreach($categorySections->chunk(2) as $chunk)
                        <div class="row marginT40">
                            @foreach($chunk as $category)
                                @php
                                    $categoryLead = $category->posts->first();
                                    $categoryOthers = $category->posts->skip(1);
                                @endphp
                                <div class="col-sm-6 col-md-6">
                                    <div class="row desktopFlexRow">
                                        <div class="col-sm-12 col-md-12">
                                            <h2 class="desktopSectionTitle">
                                                <a aria-label="{{ $category->name }}" href="{{ route('home', ['category' => $category->slug]) }}"> {{ $category->name }}</a>
                                                <a href="{{ route('home', ['category' => $category->slug]) }}" class="float-right"><i class="fa fa-angle-double-right title1_6"></i></a>
                                                <span class="bottomMarked"></span>
                                            </h2>
                                        </div>
                                        <div class="col-sm-12 col-md-12 desktopSectionLead">
                                            @if($categoryLead)
                                                <div class="thumbnail marginB0">
                                                    <div class="positionRelative">
                                                        @if($categoryLead->thumbnail_url)
                                                            <img src="{{ $categoryLead->thumbnail_url }}" class="img-responsive borderRadius5" alt="{{ $categoryLead->title }}">
                                                        @else
                                                            <img src="{{ asset('frontend/assets/images/sports-banner.jpg') }}" class="img-responsive borderRadius5" alt="{{ $categoryLead->title }}">
                                                        @endif
                                                        @if($categoryLead->isVideo())
                                                            <span class="fa fa-play pvIconMedium"></span>
                                                        @endif
                                                    </div>
                                                    <div class="caption borderC1B1">
                                                        <h3 class="title10 marginT0">{{ $categoryLead->title }}</h3>
                                                    </div>
                                                    <a href="{{ route('posts.show', $categoryLead) }}" class="linkOverlay"></a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-sm-12 col-md-12">
                                            <div class="desktopSectionListMedia listItemLastBB0">
                                                @foreach($categoryOthers as $post)
                                                    <div class="media positionRelative">
                                                        @if($post->thumbnail_url)
                                                            <div class="media-left">
                                                                <div class="positionRelative">
                                                                    <img class="media-object borderRadius5" src="{{ $post->thumbnail_url }}" width="140" alt="{{ $post->title }}">
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="media-body marginL5">
                                                            <h4 class="title11">{{ $post->title }}</h4>
                                                        </div>
                                                        <a href="{{ route('posts.show', $post) }}" class="linkOverlay"></a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="col-sm-3 col-md-3">
                <div class="row popularNewsWidgetDesktop">
                    <div class="col-sm-12 col-md-12 marginB20">
                        <div class="hidden-xs borderRadius5 borderC1-1">
                            <div class="tabNews bgWhiteImp height100P width100P">
                                <ul class="nav nav-tabs borderTRadius5" role="tablist">
                                    <li role="presentation" class="text-center borderNone active"><a aria-label="সর্বশেষ" href="#latestTab" class="title1_8" aria-controls="latestTab" role="tab" data-toggle="tab">সর্বশেষ</a></li>
                                    <li role="presentation" class="text-center borderNone"><a aria-label="পঠিত" href="#popularTab" class="title1_8" aria-controls="popularTab" role="tab" data-toggle="tab">সর্বাধিক আলোচিত</a></li>
                                </ul>
                                <div class="tab-content borderBRadius5 borderT0">
                                    <div role="tabpanel" class="tab-pane active" id="latestTab">
                                        <div class="scrollbar1 latestDivHeight">
                                            <div class="desktopSectionListMedia listItemLastBB0">
                                                @foreach($latestSidebarPosts as $post)
                                                    <div class="media positionRelative paddingLR10">
                                                        @if($post->thumbnail_url)
                                                            <div class="media-left paddingR5">
                                                                <div class="positionRelative">
                                                                    <img class="media-object borderRadius5" src="{{ $post->thumbnail_url }}" width="80" alt="{{ $post->title }}">
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="media-body">
                                                            <h4 class="margin0 marginL5 hoverBlue title1_8">{{ $post->title }}</h4>
                                                            <p class="margin0 marginL5 title1_4 colorC1 marginT5">{{ BanglaFormatter::shortDate($post->created_at) }}</p>
                                                        </div>
                                                        <a aria-label="{{ $post->title }}" href="{{ route('posts.show', $post) }}" class="linkOverlay"></a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <p class="bg2 title12 padding10 text-center marginB0 borderBRadius5 shadow1"><a class="textDecorationNone colorBlack hoverBlue" href="{{ route('home') }}">সব খবর</a></p>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="popularTab">
                                        <div class="scrollbar1 latestDivHeight">
                                            <div class="desktopSectionListMedia listItemLastBB0">
                                                @foreach($popularPosts as $post)
                                                    <div class="media positionRelative paddingLR10">
                                                        @if($post->thumbnail_url)
                                                            <div class="media-left paddingR5">
                                                                <div class="positionRelative">
                                                                    <img class="media-object borderRadius5" src="{{ $post->thumbnail_url }}" width="80" alt="{{ $post->title }}">
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="media-body">
                                                            <h4 class="margin0 marginL5 hoverBlue title1_8">{{ $post->title }}</h4>
                                                            @if($post->category)
                                                                <p class="margin0 marginL5 title1_4 colorC1 marginT5">{{ $post->category->name }}</p>
                                                            @endif
                                                        </div>
                                                        <a aria-label="{{ $post->title }}" href="{{ route('posts.show', $post) }}" class="linkOverlay"></a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <p class="bg2 title12 padding10 text-center marginB0 borderBRadius5 shadow1"><a class="textDecorationNone colorBlack hoverBlue" href="{{ route('home') }}">আরও খবর</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($activePoll)
                    <div class="row">
                        <div class="col-sm-12 col-md-12 marginB30">
                            <div class="marginCenter w300" id="pollContentDiv152">
                                <div>
                                    <p class="pollTitle"><a aria-label="অনলাইন জরিপ" href="https://bhorerkagoj.com/poll" class="colorWhite hoverBlue textDecorationNone">অনলাইন জরিপ</a> <span class="downloadPoll" data-pollid="152" data-polldate="২৬ আগস্ট ২০২৫"><i class="fa fa-download"></i></span></p>
                                    <div>
                                        <a class="textDecorationNone" href="https://bhorerkagoj.com/poll/152">
                                            <img src="https://bhorerkagoj.com/uploads/settings/thumbnail.jpg" data-src="https://bhorerkagoj.com/uploads/polls/1756214840-68adb638672b3.jpg" class="img-responsive" alt="নির্বাচনবিরোধী কথা যে-ই বলুক, তারা রাজনীতির মাঠ থেকে মাইনাস হয়ে যাবেন, সালাহউদ্দিন আহমদের এ মন্তব্যের সঙ্গে আপনি কি একমত?">
                                        </a>
                                    </div>
                                    <div class="paddingB0 pollTextDiv">
                                        <div class="thumbnail padding0 border0 marginB0">
                                            <div class="caption text-left paddingT0">
                                                @if(!empty($activePoll->poll_date_bangla))
                                                    <p class="desktopTime color1 marginB10"><i class="fa fa-regular fa-clock"></i>
                                                        <span class="pollDate">{{ $activePoll->poll_date_bangla }}</span>
                                                    </p>
                                                @endif
                                                <h3 class="title12 marginT0"><a class="textDecorationNone colorBlack" href="https://bhorerkagoj.com/poll/152"><span>{{ $activePoll->question }}</span></a></h3>

                                                <div class="marginT10">
                                                    <p class="pollOption"><label class="clickVote" data-pollid="152" data-votetype="yes"><input class="clickVoteInput152" type="radio" name="poll_vote" value="yes"> হ্যাঁ ভোট <span class="pull-right totalyesVote152" style="display: none;">৩৫ %</span></label></p>

                                                    <p class="pollOption"><label class="clickVote" data-pollid="152" data-votetype="no"><input class="clickVoteInput152" type="radio" name="poll_vote" value="no"> না ভোট <span class="pull-right totalNoVote152" style="display: none;">৬১ %</span></label></p>

                                                    <p class="pollOption"><label class="clickVote" data-pollid="152" data-votetype="no_comment"><input class="clickVoteInput152" type="radio" name="poll_vote" value="no_comment"> মন্তব্য নেই <span class="pull-right totalNoCommentVote152" style="display: none;">৪ %</span></label></p>
                                                </div>

                                                <div class="text-center marginT20 marginB20">
                                                    <p class="title12 color1">মোট ভোটদাতাঃ <span class="totalVoter152">{{ $activePoll->total_vote_bangla }}</span> জন</p>
                                                </div>

                                                <div class="text-center marginT10 pollDownloadTime" style="display: none;">
                                                    <p class="marginT30 marginB0"><img src="https://bhorerkagoj.com/uploads/settings/logo-black.png" style="height: 50px;" class="img-responsive marginCenter" alt="Logo"></p>
                                                    <p class="title1_6 colorBlack">ডাউনলোডঃ ২৫ অক্টোবর ২০২৫, ১৮:৩১ পিএম</p>
                                                </div>

                                                <div class="row downloadPollShareIcon">
                                                    <div class="col-xs-12 text-center marginB10">
                                                        <!-- sharethis -->
                                                        <div class="sharethis-inline-share-buttons st-right st-hidden st-inline-share-buttons" data-url="https://bhorerkagoj.com/poll/152" data-title="নির্বাচনবিরোধী কথা যে-ই বলুক, তারা রাজনীতির মাঠ থেকে মাইনাস হয়ে যাবেন, সালাহউদ্দিন আহমদের এ মন্তব্যের সঙ্গে আপনি কি একমত?" id="st-1"><div class="st-total st-hidden">
                                                                <span class="st-label"></span>
                                                                <span class="st-shares">Shares</span>
                                                            </div><div class="st-btn st-first st-remove-label" data-network="facebook" style="display: none;">
                                                                <img alt="facebook sharing button" src="https://platform-cdn.sharethis.com/img/facebook.svg">
                                                            </div><div class="st-btn st-remove-label" data-network="messenger" style="display: none;">
                                                                <img alt="messenger sharing button" src="https://platform-cdn.sharethis.com/img/messenger.svg">
                                                            </div><div class="st-btn st-remove-label" data-network="twitter" style="display: none;">
                                                                <img alt="twitter sharing button" src="https://platform-cdn.sharethis.com/img/twitter.svg">
                                                            </div><div class="st-btn st-remove-label" data-network="whatsapp" style="display: none;">
                                                                <img alt="whatsapp sharing button" src="https://platform-cdn.sharethis.com/img/whatsapp.svg">
                                                            </div><div class="st-btn st-remove-label" data-network="copy" style="display: none;">
                                                                <img alt="copy sharing button" src="https://platform-cdn.sharethis.com/img/copy.svg">
                                                            </div><div class="st-btn st-last st-remove-label" data-network="print" style="display: none;">
                                                                <img alt="print sharing button" src="https://platform-cdn.sharethis.com/img/print.svg">
                                                            </div></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <span onclick="gotop()" class="go-to-top hidden-print" style="display: none;">
        <i class="fa fa-angle-up" aria-hidden="true"></i>
    </span>
@endsection

@push('scripts')
    <script>
        $(window).on('load', function () {
            $('.flexslider').flexslider({
                controlNav: false,
                animation: 'slide',
                animationLoop: false,
                directionNav: true,
                itemWidth: 260,
                itemMargin: 20,
                pausePlay: true,
                nextText: '',
                prevText: '',
            });
        });

        $(window).on('scroll', function () {
            if ($(window).scrollTop() > 300) {
                $('.go-to-top').show();
            } else {
                $('.go-to-top').hide();
            }
        });

        function gotop() {
            var scrollStep = -window.scrollY / 300,
                scrollInterval = setInterval(function () {
                    if (window.scrollY !== 0) {
                        window.scrollBy(0, scrollStep);
                    } else {
                        clearInterval(scrollInterval);
                    }
                }, 2);
        }
    </script>
@endpush
