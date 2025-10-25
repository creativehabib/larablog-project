@extends('layouts.frontend')

@php
    use App\Support\BanglaFormatter;
    use Illuminate\Support\Facades\Storage;
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
                            <div class="marginCenter w300" id="pollContent">
                                <div class="pollCard">
                                    <p class="pollTitle">
                                        <a aria-label="অনলাইন জরিপ" href="{{ route('polls.index') }}" class="colorWhite hoverBlue textDecorationNone">অনলাইন জরিপ</a>
                                        @if(!empty($activePoll->poll_date_bangla))
                                            <span class="pollDate marginL5">{{ $activePoll->poll_date_bangla }}</span>
                                        @endif
                                    </p>

                                    @if($activePoll->image)
                                        <div class="marginB15">
                                            <img
                                                src="{{ Storage::url($activePoll->image) }}"
                                                class="img-responsive borderRadius5"
                                                alt="{{ $activePoll->question }}"
                                            >
                                        </div>
                                    @endif

                                    <div class="paddingB0 pollTextDiv">
                                        <div class="thumbnail padding0 border0 marginB0">
                                            <div class="caption text-left paddingT0">
                                                <h3 class="title12 marginT0">
                                                    <a class="textDecorationNone colorBlack" href="{{ route('polls.index') }}">
                                                        <span>{{ $activePoll->question }}</span>
                                                    </a>
                                                </h3>

                                                @if($activePoll->source_url)
                                                    <p class="marginT5 title1_4">
                                                        <a href="{{ $activePoll->source_url }}" target="_blank" rel="noopener" class="hoverBlue">সূত্র দেখুন</a>
                                                    </p>
                                                @endif

                                                <form
                                                    id="pollVoteForm"
                                                    action="{{ route('polls.vote', $activePoll) }}"
                                                    method="POST"
                                                    class="marginT10"
                                                    data-poll-id="{{ $activePoll->id }}"
                                                    data-success-message="আপনার ভোটের জন্য ধন্যবাদ!"
                                                    data-error-message="দুঃখিত! ভোট সম্পন্ন করা যায়নি। অনুগ্রহ করে পুনরায় চেষ্টা করুন।"
                                                    data-already-voted-message="আপনি ইতোমধ্যেই এই জরিপে ভোট দিয়েছেন।"
                                                    data-auto-submit="true"
                                                >
                                                    @csrf
                                                    <p class="pollOption">
                                                        <label class="clickVote">
                                                            <input type="radio" name="option" value="yes">
                                                            হ্যাঁ ভোট
                                                            <span class="pull-right" data-poll-count-bangla="yes">{{ $activePoll->yes_vote_bangla }}</span>
                                                            <span style="display: none;" data-poll-percent-bangla="yes">{{ $activePoll->yes_vote_percent_bangla }}%</span>
                                                        </label>
                                                    </p>

                                                    <p class="pollOption">
                                                        <label class="clickVote">
                                                            <input type="radio" name="option" value="no">
                                                            না ভোট
                                                            <span class="pull-right" data-poll-count-bangla="no">{{ $activePoll->no_vote_bangla }}</span>
                                                            <span style="display: none;" data-poll-percent-bangla="no">{{ $activePoll->no_vote_percent_bangla }}%</span>
                                                        </label>
                                                    </p>

                                                    <p class="pollOption">
                                                        <label class="clickVote">
                                                            <input type="radio" name="option" value="no_opinion">
                                                            মন্তব্য নেই
                                                            <span class="pull-right" data-poll-count-bangla="no_opinion">{{ $activePoll->no_opinion_bangla }}</span>
                                                            <span style="display: none;" data-poll-percent-bangla="no_opinion">{{ $activePoll->no_opinion_vote_percent_bangla }}%</span>
                                                        </label>
                                                    </p>

                                                    <button type="submit" class="btn btn-primary btn-block marginT10" style="display: none;">ভোট দিন</button>
                                                </form>

                                                <div class="text-center marginT20 marginB20">
                                                    <p class="title12 color1">
                                                        মোট ভোটদাতাঃ <span data-poll-total-bangla>{{ $activePoll->total_vote_bangla }}</span> জন
                                                    </p>
                                                </div>

                                                <p class="text-center marginB0">
                                                    <a class="textDecorationNone hoverBlue" href="{{ route('polls.index') }}">সব জরিপ দেখুন</a>
                                                </p>
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
