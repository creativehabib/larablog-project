@extends('layouts.frontend')

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

            @include('front.partials.sidebar')
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
