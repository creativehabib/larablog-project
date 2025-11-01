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
                                <a class="desktopTrendingTopicItem" aria-label="{{ $topic->name }}" href="{{ route('categories.show', $topic) }}">{{ $topic->name }}</a>
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
                    <div class="col-sm-12 col-md-12 paddingLR10">
                        <div class="marginB15">
                            <h1 class="desktopSectionTitle">{{ $category->name }} <span class="bottomMarked"></span></h1>
                            @if($category->description)
                                <p class="desktopSummary marginT5">{{ $category->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                @if($leadStory)
                    <div class="row marginLR-10">
                        <div class="col-sm-8 col-md-8 paddingLR10 desktopSectionLead">
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
                                    <h2 class="title9 marginT5 marginB5"><strong>{{ $leadStory->title }}</strong></h2>
                                    <p class="desktopSummary marginB5 title1_8">{{ $leadStory->excerpt }}</p>
                                </div>
                                <a href="{{ route('posts.show', $leadStory) }}" class="linkOverlay"></a>
                            </div>
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
                                                <strong>{{ $story->title }}</strong>
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
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-md-12 paddingLR10"><p class="desktopDivider marginT10 marginB10"></p></div>
                    </div>
                @else
                    <div class="row marginLR-10">
                        <div class="col-sm-12 col-md-12">
                            <div class="thumbnail borderC1B1">
                                <div class="caption">
                                    <p class="desktopSummary marginB5">এই ক্যাটাগরিতে এখনও কোনো প্রকাশিত পোস্ট পাওয়া যায়নি।</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

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
                        <div class="col-sm-12 col-md-12"><p class="desktopDivider marginT0 marginB10"></p></div>
                    </div>
                @endif

                <div class="row marginT30">
                    <div class="col-sm-12 col-md-12">
                        <h2 class="desktopSectionTitle">সর্বশেষ {{ $category->name }} সংবাদ <span class="bottomMarked"></span></h2>
                    </div>
                </div>

                <div class="row marginLR-10 desktopFlexRow">
                    @forelse($latestPosts as $post)
                        <div class="col-sm-4 col-md-4 paddingLR10 desktopSectionLead">
                            <div class="thumbnail marginB15">
                                <div class="positionRelative">
                                    @if($post->thumbnail_url)
                                        <img src="{{ $post->thumbnail_url }}" class="img-responsive borderRadius5" alt="{{ $post->title }}">
                                    @else
                                        <img src="{{ asset('frontend/assets/images/sports-banner.jpg') }}" class="img-responsive borderRadius5" alt="{{ $post->title }}">
                                    @endif
                                    @if($post->isVideo())
                                        <span class="fa fa-play pvIconMedium"></span>
                                    @endif
                                </div>
                                <div class="caption">
                                    <h3 class="title12 marginT0 truncate-2-lines"><strong>{{ $post->title }}</strong></h3>
                                </div>
                                <a href="{{ route('posts.show', $post) }}" class="linkOverlay"></a>
                            </div>
                        </div>
                    @empty
                        <div class="col-sm-12 col-md-12">
                            <div class="thumbnail borderC1B1">
                                <div class="caption">
                                    <p class="desktopSummary marginB0">এই ক্যাটাগরিতে কোনো প্রকাশিত পোস্ট পাওয়া যায়নি।</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if($latestPosts->hasPages())
                    <div class="row">
                        <div class="col-sm-12 col-md-12 text-center">
                            {{ $latestPosts->onEachSide(1)->links() }}
                        </div>
                    </div>
                @endif
            </div>

            @include('front.partials.sidebar')
        </div>
    </div>

    <span onclick="gotop()" class="go-to-top hidden-print" style="display: none;">
        <i class="fa fa-angle-up" aria-hidden="true"></i>
    </span>
@endsection
