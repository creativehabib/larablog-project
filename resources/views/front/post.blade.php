@extends('layouts.frontend')
@php
    use App\Support\BanglaFormatter;
    use Illuminate\Support\Facades\Storage;
@endphp
@section('content')

    <!-- main div -->
    <div class="mainDiv">



        <!-- visible print -->
        <div class="text-center visiblePrintDiv" style="margin-top: -80px;display: none;">
            <img src="https://www.bhorerkagoj.com/uploads/settings/logo-black.png" alt="Logo" width="200">
            <p class="title12 borderC1B1 padding10">প্রিন্ট: ৩১ অক্টোবর ২০২৫, ০৭:৫৫ পিএম</p>
        </div>
        <!-- visible print end -->

        <!-- desktop version start -->
        <div class="bgWHite hidden-xs marginT40">
            <!-- article reached -->
            <div class="articleReached1" data-url="https://www.bhorerkagoj.net/national/775196"></div>


            <!-- justnow news -->

            <!-- breaking news -->


            <div class="container">
                <div class="row">

                    <!-- category news -->
                    <div class="col-md-2 col-lg-2 hidden-print">
                        <div class="categoryNewsWidgetDesktop1">
                            <h2 class="desktopSectionTitle title11 marginB10" style="margin-top: -3px">আরো পড়ুন <span class="bottomMarked"></span></h2>
                            <div class="row desktopSectionListMedia ajaxCategoryNewsDivDesktop1"></div>
                        </div>
                    </div>


                    <!-- news section -->
                    <div class="col-md-7 col-lg-7">

                        <!-- title section -->
                        <div class="marginB20">
                            <p class="desktopDetailCat marginB15 hidden-print">
                                <a aria-label="{{ $post->category->name }}" href="/{{ $post->category->slug }}">
                                    <strong>{{ $post->category->name }}</strong>
                                </a>
                            </p>
                            <h1 class="desktopDetailHeadline marginT0 title8"><strong>{{ $post->title }}</strong></h1>
                        </div>

                        <!-- time section -->
                        <div class="row">
                            <div class="col-sm-7 col-md-7 marginT10">
                                <div class="desktopDetailAuthorDiv">
                                    @if($post->author)
                                        <img src="{{ $post->author->avatar }}" class="img-responsive borderRadius50P" width="50" alt="{{ $post->author->name }}">
                                    @endif
                                </div>
                                <div class="displayInlineBlock">
                                    @if($post->author)
                                        <p class="desktopDetailReporter">{{ $post->author->name }}</p>
                                    @endif
                                    <p class="desktopDetailPTime color1">প্রকাশ: {{ BanglaFormatter::shortDate($post->updated_at) }}</p>
                                </div>
                            </div>

                            <!-- share -->
                            <div class="col-sm-5 col-md-5 hidden-print">
                                <div class="marginT15">
                                    <!-- sharethis -->
                                    <div class="sharethis-inline-share-buttons">Share button</div>
                                </div>
                            </div>
                        </div>

                        <p class="desktopDivider"></p>

                        <!-- photo section -->
                        <div class="desktopDetailPhotoDiv">
                            <div class="desktopDetailPhoto">
                                @if ($post->isVideo() && $post->video_embed_html)
                                    <div class="desktopDetailVideo hidden-print">
                                        <div>{!! $post->video_embed_html !!}</div>
                                    </div>
                                @elseif ($post->thumbnail_url)
                                    <figure class="mt-8">
                                        <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="img-responsive">
                                    </figure>
                                    <figcaption>
                                        <p>ছবি: সংগৃহীত</p>
                                    </figcaption>
                                @endif
                            </div>
                        </div>

                        <!-- news body -->
                        <div class="desktopDetailBody">
                            <div style="display: inline;">
                                {!! $post->description !!}
                            </div>
                        </div>

                        <!-- news document -->


                        <!-- news tag -->
                        <div class="desktopDetailTag hidden-print">
                            <p>
                                <a class="desktopTagItem" aria-label="প্রেস সচিব " href="https://www.bhorerkagoj.com/topic/প্রেস-সচিব">প্রেস সচিব </a>
                                <a class="desktopTagItem" aria-label="শফিকুল আলম" href="https://www.bhorerkagoj.com/topic/শফিকুল-আলম">শফিকুল আলম</a>
                                <a class="desktopTagItem" aria-label="গণভোট" href="https://www.bhorerkagoj.com/topic/গণভোট">গণভোট</a>
                            </p>
                        </div>

                        <!-- ad rits -->
                        <div class="row hidden-print">
                            <div class="col-sm-12 col-md-12 marginT20">
                                <div class="adDiv h250 borderRadius5 overflowHidden">
                                    <!--<div class="futureads" style="width:300px;height:250px;display:inline-block;" data-ad-slot="pw_31253"></div> <script type="text/javascript">(wapTag.Init = window.wapTag.Init || []).push(function () { wAPITag.display("pw_31253") })</script>-->

                                    <!-- <ins class="adsbygoogle"
                                    style="display:inline-block;width:300px;height:250px"
                                    data-ad-client="ca-pub-6140828263407584"
                                    data-ad-slot="9190197910"></ins>
                                    <script>
                                        (adsbygoogle = window.adsbygoogle || []).push({});
                                    </script> -->
                                </div>
                            </div>
                        </div>
                        <!-- ad -->

                        <!-- load second detail -->
                        <div class="loadAjaxDetail1"></div>


                        <!-- timeline news -->

                        <!-- subscribe & follow -->
                        <div class="hidden-print marginT40">
                            <h2 class="desktopSectionTitle">সাবস্ক্রাইব ও অনুসরণ করুন <span class="bottomMarked"></span></h2>
                            <div class="row marginLR-10 desktopFlexRow">
                                <div class="col-sm-6 col-md-6 paddingLR10">
                                    <div style="border: 1px solid #ebedf0;height: 100%;padding: 8px 8px 0px 8px">
                                        <script src="https://apis.google.com/js/platform.js"></script>
                                        <div class="g-ytsubscribe" data-channelid="UCXs9EVaxc1rQjTM9Ber2pAg" data-layout="full" data-count="hidden"></div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6 paddingLR10 hidden-print">
                                    <div class="fb-page" data-href="https://www.facebook.com/DailyBhorerKagoj/" data-tabs="" data-width="" data-height="" data-small-header="true" data-adapt-container-width="true" data-hide-cover="true" data-show-facepile="false"><blockquote cite="https://www.facebook.com/DailyBhorerKagoj/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/DailyBhorerKagoj/">Bhorer Kagoj</a></blockquote></div>
                                </div>
                            </div>
                        </div>


                        <!-- taboola ad -->
                        <!--<div class="hidden-print marginT40">
                            <div class="taboola-ad-container1"></div>
                        </div>-->


                        <!-- related news -->
                        <!-- <div class="hidden-print relatedNewsWidgetDesktop1 marginT40" style="display: none;">
                            <h2 class="desktopSectionTitle">এ সম্পর্কিত আরো খবর <span class="bottomMarked"></span></h2>
                            <div class="row marginLR-10 desktopSectionListMedia desktopFlexRow ajaxRelatedNewsDivDesktop1"></div>
                        </div> -->

                        <!-- comments -->
                        <div class="hidden-print marginT30">
                            <h2 class="desktopSectionTitle">মন্তব্য করুন <span class="bottomMarked"></span></h2>
                            <div class="commentsDiv borderC1-1 borderRadius5">
                                <div class="fb-comments" data-href="https://www.bhorerkagoj.net/national/775196" data-numposts="2" width="100%"></div>
                            </div>
                        </div>

                    </div>


                    <!-- sidebar -->
                    <div class="col-md-3 col-lg-3 hidden-print">
                        <!-- latest popular news -->
                        <div class="row popularNewsWidgetDesktop">
                            <div class="col-sm-12 col-md-12 marginB30">
                                <div class="hidden-xs borderRadius5 borderC1-1">
                                    <div class="tabNews bgWhiteImp height100P width100P">
                                        <ul class="nav nav-tabs borderTRadius5" role="tablist">
                                            <li role="presentation" class="text-center borderNone active"><a aria-label="সর্বশেষ" class="" href="#latestTab" aria-controls="highestTab" role="tab" data-toggle="tab" aria-expanded="false">সর্বশেষ</a></li>
                                            <li role="presentation" class="text-center borderNone"><a aria-label="পঠিত" class="" href="#highestTab" aria-controls="highestTab" role="tab" data-toggle="tab" aria-expanded="false">সর্বাধিক পঠিত</a></li>
                                        </ul>

                                        <div class="tab-content borderBRadius5 borderT0">
                                            <div role="tabpanel" class="tab-pane active" id="latestTab">
                                                <div class="scrollbar1 latestDivHeight">
                                                    <div class="desktopSectionListMedia listItemLastBB0">
                                                        <div class="media positionRelative paddingLR10">
                                                            <div class="media-left paddingR5">
                                                                <div class="positionRelative">
                                                                    <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/omra-690489d504dfd.jpg" width="80" alt="ওমরাহ ভিসার মেয়াদ কমালো সৌদি আরব">
                                                                </div>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="margin0 marginL5 hoverBlue title12">ওমরাহ ভিসার মেয়াদ কমালো সৌদি আরব</h4>
                                                            </div>
                                                            <a aria-label="ওমরাহ ভিসার মেয়াদ কমালো সৌদি আরব" href="https://www.bhorerkagoj.com/international/775198" class="linkOverlay"></a>
                                                        </div>
                                                        <div class="media positionRelative paddingLR10">
                                                            <div class="media-left paddingR5">
                                                                <div class="positionRelative">
                                                                    <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/cultivate-hen-bk-690485bd6d577.jpg" width="80" alt="সবজি-মুরগিতে স্বস্তি, বেড়েছে আটার দাম">
                                                                </div>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="margin0 marginL5 hoverBlue title12">সবজি-মুরগিতে স্বস্তি, বেড়েছে আটার দাম</h4>
                                                            </div>
                                                            <a aria-label="সবজি-মুরগিতে স্বস্তি, বেড়েছে আটার দাম" href="https://www.bhorerkagoj.com/economics/775197" class="linkOverlay"></a>
                                                        </div>
                                                        <div class="media positionRelative paddingLR10">
                                                            <div class="media-left paddingR5">
                                                                <div class="positionRelative">
                                                                    <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/shafiq-6904838c9f43a.jpg" width="80" alt="নির্বাচন ১৫ ফেব্রুয়ারির আগে হবে, কোনও শক্তি নেই এটাকে পেছানোর: শফিকুল আলম">
                                                                </div>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="margin0 marginL5 hoverBlue title12">নির্বাচন ১৫ ফেব্রুয়ারির আগে হবে, কোনও শক্তি নেই এটাকে পেছানোর: শফিকুল আলম</h4>
                                                            </div>
                                                            <a aria-label="নির্বাচন ১৫ ফেব্রুয়ারির আগে হবে, কোনও শক্তি নেই এটাকে পেছানোর: শফিকুল আলম" href="https://www.bhorerkagoj.com/national/775196" class="linkOverlay"></a>
                                                        </div>
                                                        <div class="media positionRelative paddingLR10">
                                                            <div class="media-left paddingR5">
                                                                <div class="positionRelative">
                                                                    <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/fakrul-6904811e26949.jpg" width="80" alt="অন্তর্বর্তী সরকার জনগণের সঙ্গে বিশ্বাসঘাতকতা করেছে: মির্জা ফখরুল">
                                                                </div>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="margin0 marginL5 hoverBlue title12">অন্তর্বর্তী সরকার জনগণের সঙ্গে বিশ্বাসঘাতকতা করেছে: মির্জা ফখরুল</h4>
                                                            </div>
                                                            <a aria-label="অন্তর্বর্তী সরকার জনগণের সঙ্গে বিশ্বাসঘাতকতা করেছে: মির্জা ফখরুল" href="https://www.bhorerkagoj.com/politics/775195" class="linkOverlay"></a>
                                                        </div>
                                                        <div class="media positionRelative paddingLR10">
                                                            <div class="media-left paddingR5">
                                                                <div class="positionRelative">
                                                                    <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/Sorok-69038f42cbacf.jpg" width="80" alt="ব্র্যাক ড্রাইভিং স্কুলের সড়ক নিরাপত্তা বিষয়ক সচেতনতামূলক মানববন্ধন">
                                                                </div>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="margin0 marginL5 hoverBlue title12">ব্র্যাক ড্রাইভিং স্কুলের সড়ক নিরাপত্তা বিষয়ক সচেতনতামূলক মানববন্ধন</h4>
                                                            </div>
                                                            <a aria-label="ব্র্যাক ড্রাইভিং স্কুলের সড়ক নিরাপত্তা বিষয়ক সচেতনতামূলক মানববন্ধন" href="https://www.bhorerkagoj.com/national-other/775194" class="linkOverlay"></a>
                                                        </div>
                                                        <div class="media positionRelative paddingLR10">
                                                            <div class="media-left paddingR5">
                                                                <div class="positionRelative">
                                                                    <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/Medical-Tour-69038ee721867.jpg" width="80" alt="মেডিকেল ট্যুরিজমে চমক এনেছে সুহা ট্রাভেলস থাইল্যান্ড">
                                                                </div>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="margin0 marginL5 hoverBlue title12">মেডিকেল ট্যুরিজমে চমক এনেছে সুহা ট্রাভেলস থাইল্যান্ড</h4>
                                                            </div>
                                                            <a aria-label="মেডিকেল ট্যুরিজমে চমক এনেছে সুহা ট্রাভেলস থাইল্যান্ড" href="https://www.bhorerkagoj.com/tourism/775193" class="linkOverlay"></a>
                                                        </div>
                                                        <div class="media positionRelative paddingLR10">
                                                            <div class="media-left paddingR5">
                                                                <div class="positionRelative">
                                                                    <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/JCI-690388dbe515a.jpg" width="80" alt="সিংগাইরে মাদ্রাসায় বসার বেঞ্চ-টুল উপহার দিলো জেসিআই মানিকগঞ্জ">
                                                                </div>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="margin0 marginL5 hoverBlue title12">সিংগাইরে মাদ্রাসায় বসার বেঞ্চ-টুল উপহার দিলো জেসিআই মানিকগঞ্জ</h4>
                                                            </div>
                                                            <a aria-label="সিংগাইরে মাদ্রাসায় বসার বেঞ্চ-টুল উপহার দিলো জেসিআই মানিকগঞ্জ" href="https://www.bhorerkagoj.com/corporate/775192" class="linkOverlay"></a>
                                                        </div>
                                                        <div class="media positionRelative paddingLR10">
                                                            <div class="media-left paddingR5">
                                                                <div class="positionRelative">
                                                                    <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/Rahman-Mridha-Bangladesh-690382e533377.jpg" width="80" alt="স্বাধীনতার এক নির্মম পরিহাস">
                                                                </div>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="margin0 marginL5 hoverBlue title12">স্বাধীনতার এক নির্মম পরিহাস</h4>
                                                            </div>
                                                            <a aria-label="স্বাধীনতার এক নির্মম পরিহাস" href="https://www.bhorerkagoj.com/thinking/775190" class="linkOverlay"></a>
                                                        </div>
                                                        <div class="media positionRelative paddingLR10">
                                                            <div class="media-left paddingR5">
                                                                <div class="positionRelative">
                                                                    <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/Dr-Yunus-690381dc2343d.jpg" width="80" alt="ড. ইউনূস হতে পারেন জাতিসংঘের মহাসচিব!">
                                                                </div>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="margin0 marginL5 hoverBlue title12">ড. ইউনূস হতে পারেন জাতিসংঘের মহাসচিব!</h4>
                                                            </div>
                                                            <a aria-label="ড. ইউনূস হতে পারেন জাতিসংঘের মহাসচিব!" href="https://www.bhorerkagoj.com/thinking/775189" class="linkOverlay"></a>
                                                        </div>
                                                        <div class="media positionRelative paddingLR10">
                                                            <div class="media-left paddingR5">
                                                                <div class="positionRelative">
                                                                    <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/Legal-advisor-6903644b430a2.jpg" width="80" alt="গণভোটের বিষয়ে চূড়ান্ত সিদ্ধান্ত দেবেন প্রধান উপদেষ্টা : আইন উপদেষ্টা">
                                                                </div>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="margin0 marginL5 hoverBlue title12">গণভোটের বিষয়ে চূড়ান্ত সিদ্ধান্ত দেবেন প্রধান উপদেষ্টা : আইন উপদেষ্টা</h4>
                                                            </div>
                                                            <a aria-label="গণভোটের বিষয়ে চূড়ান্ত সিদ্ধান্ত দেবেন প্রধান উপদেষ্টা : আইন উপদেষ্টা" href="https://www.bhorerkagoj.com/government/775188" class="linkOverlay"></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="bg2 title11 padding10 text-center marginB0 borderBRadius5 shadow1"><a class="textDecorationNone colorBlack hoverBlue" href="https://www.bhorerkagoj.com/latest">সব খবর</a></p>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="highestTab">
                                                <div class="scrollbar1 latestDivHeight">
                                                    <div class="desktopSectionListMedia listItemLastBB0 ajaxPopularNewsDivDesktop"><div class="popularNewsLoaderDiv text-center marginT50"><i class="fa fa-spinner"></i></div></div>
                                                </div>
                                                <p class="bg2 title11 padding10 text-center marginB0 borderBRadius5 shadow1"><a class="textDecorationNone colorBlack hoverBlue" href="https://www.bhorerkagoj.com/popular">সব খবর</a></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="visible-xs borderRadius5 borderC1-1">
                                    <div class="tabNews bgWhiteImp height100P width100P">
                                        <ul class="nav nav-tabs borderTRadius5" role="tablist">
                                            <li role="presentation" class="text-center borderNone active"><a aria-label="সর্বশেষ" class="" href="#latestTab" aria-controls="latestTab" role="tab" data-toggle="tab" aria-expanded="false">সর্বশেষ</a></li>
                                            <li role="presentation" class="text-center borderNone"><a aria-label="পঠিত" class="" href="#highestTab" aria-controls="highestTab" role="tab" data-toggle="tab" aria-expanded="false">সর্বাধিক পঠিত</a></li>
                                        </ul>

                                        <div class="tab-content borderBRadius5 borderT0">
                                            <div role="tabpanel" class="tab-pane active" id="latestTab">
                                                <div class="scrollbar1">
                                                    <div class="sectionListMedia listItemLastBB0">
                                                        <div class="media positionRelative paddingLR10">
                                                            <div class="media-left paddingR5">
                                                                <div class="positionRelative">
                                                                    <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/omra-690489d504dfd.jpg" width="140" alt="ওমরাহ ভিসার মেয়াদ কমালো সৌদি আরব">
                                                                </div>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="margin0 marginL5 hoverBlue">ওমরাহ ভিসার মেয়াদ কমালো সৌদি আরব</h4>
                                                            </div>
                                                            <a aria-label="ওমরাহ ভিসার মেয়াদ কমালো সৌদি আরব" href="https://www.bhorerkagoj.com/international/775198" class="linkOverlay"></a>
                                                        </div>
                                                        <div class="media positionRelative paddingLR10">
                                                            <div class="media-left paddingR5">
                                                                <div class="positionRelative">
                                                                    <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/cultivate-hen-bk-690485bd6d577.jpg" width="140" alt="সবজি-মুরগিতে স্বস্তি, বেড়েছে আটার দাম">
                                                                </div>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="margin0 marginL5 hoverBlue">সবজি-মুরগিতে স্বস্তি, বেড়েছে আটার দাম</h4>
                                                            </div>
                                                            <a aria-label="সবজি-মুরগিতে স্বস্তি, বেড়েছে আটার দাম" href="https://www.bhorerkagoj.com/economics/775197" class="linkOverlay"></a>
                                                        </div>
                                                        <div class="media positionRelative paddingLR10">
                                                            <div class="media-left paddingR5">
                                                                <div class="positionRelative">
                                                                    <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/shafiq-6904838c9f43a.jpg" width="140" alt="নির্বাচন ১৫ ফেব্রুয়ারির আগে হবে, কোনও শক্তি নেই এটাকে পেছানোর: শফিকুল আলম">
                                                                </div>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="margin0 marginL5 hoverBlue">নির্বাচন ১৫ ফেব্রুয়ারির আগে হবে, কোনও শক্তি নেই এটাকে পেছানোর: শফিকুল আলম</h4>
                                                            </div>
                                                            <a aria-label="নির্বাচন ১৫ ফেব্রুয়ারির আগে হবে, কোনও শক্তি নেই এটাকে পেছানোর: শফিকুল আলম" href="https://www.bhorerkagoj.com/national/775196" class="linkOverlay"></a>
                                                        </div>
                                                        <div class="media positionRelative paddingLR10">
                                                            <div class="media-left paddingR5">
                                                                <div class="positionRelative">
                                                                    <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/fakrul-6904811e26949.jpg" width="140" alt="অন্তর্বর্তী সরকার জনগণের সঙ্গে বিশ্বাসঘাতকতা করেছে: মির্জা ফখরুল">
                                                                </div>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="margin0 marginL5 hoverBlue">অন্তর্বর্তী সরকার জনগণের সঙ্গে বিশ্বাসঘাতকতা করেছে: মির্জা ফখরুল</h4>
                                                            </div>
                                                            <a aria-label="অন্তর্বর্তী সরকার জনগণের সঙ্গে বিশ্বাসঘাতকতা করেছে: মির্জা ফখরুল" href="https://www.bhorerkagoj.com/politics/775195" class="linkOverlay"></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="bg2 title12 padding10 text-center marginB0 borderBRadius5 shadow1"><a class="textDecorationNone colorBlack hoverBlue" href="https://www.bhorerkagoj.com/latest">সব খবর</a></p>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="highestTab">
                                                <div class="scrollbar1">
                                                    <div class="sectionListMedia listItemLastBB0 ajaxPopularNewsDivMobile"><div class="popularNewsLoaderDiv text-center marginT50"><i class="fa fa-spinner"></i></div></div>
                                                </div>
                                                <p class="bg2 title12 padding10 text-center marginB0 borderBRadius5 shadow1"><a class="textDecorationNone colorBlack hoverBlue" href="https://www.bhorerkagoj.com/popular">সব খবর</a></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- latest popular news end -->

                        <!-- facebook page -->
                        <!-- <div class="row">
                            <div class="col-sm-12 col-md-12 marginB30">
                                <div class="marginCenter w300">
                                    <div class="fb-page" data-href="https://www.facebook.com/DailyBhorerKagoj/" data-tabs="" data-width="300" data-height="" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/DailyBhorerKagoj/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/DailyBhorerKagoj/">Bhorer Kagoj</a></blockquote></div>
                                </div>
                            </div>
                        </div> -->
                        <!-- facebook page end -->


                        <!-- purple patch -->
                        <div class="row">
                            <div class="col-sm-12 col-md-12 marginB30">
                                <div class="adDiv w300 borderRadius5 overflowHidden">
                                    <ins data-purplepatch-slotid="18"
                                         data-purplepatch-ct0="%%CLICK_URL_UNESC%%"
                                         data-purplepatch-id="53126d71827fcba70ff68055b9a73ca1pdt"></ins>
                                    <script async="" src="//adserver.purplepatch.online/async.js" type="text/javascript"></script>
                                </div>
                            </div>
                        </div>


                        <!-- ad -->
                        <div class="row">
                            <div class="col-sm-12 col-md-12 marginB30">
                                <div class="adDiv w300 borderRadius5 overflowHidden">
                                    <ins class="adsbygoogle"
                                         style="display:inline-block;width:300px;height:250px"
                                         data-ad-client="ca-pub-6140828263407584"
                                         data-ad-slot="9190197910"></ins>
                                    <script>
                                        (adsbygoogle = window.adsbygoogle || []).push({});
                                    </script>
                                </div>
                            </div>
                        </div>
                        <!-- ad -->

                        <!-- special news -->

                    </div>

                </div>
            </div>

            <!-- article reached -->
            <div class="articleReached1" data-url="https://www.bhorerkagoj.net/national/775196"></div>

            <!-- append second detail -->
            <div class="appendAjaxDetail1 hidden-print"><p class="text-center marginB0"><i class="fa fa-spinner"></i></p></div>
        </div>
        <!-- desktop version start end -->




        <!-- mobile version start -->
        <div class="bgWHite visible-xs paddingT20">
            <!-- article reached -->
            <div class="articleReached1" data-url="https://www.bhorerkagoj.net/national/775196"></div>

            <!-- justnow news -->

            <!-- breaking news -->


            <div class="container">
                <div class="row">

                    <div class="col-sm-12 col-md-12 paddingLR20 marginB15">
                        <div class="adDiv w320">
                            <ins data-purplepatch-slotid="22"
                                 data-purplepatch-ct0="%%CLICK_URL_UNESC%%"
                                 data-purplepatch-id="53126d71827fcba70ff68055b9a73ca1pdt"></ins>
                            <script async="" src="//adserver.purplepatch.online/async.js" type="text/javascript"></script>
                        </div>
                    </div>

                    <!-- title section -->
                    <div class="col-sm-12 col-md-12 paddingLR20">
                        <div>
                            <p class="desktopDetailCat marginB15 hidden-print"><a aria-label="জাতীয়" href="https://www.bhorerkagoj.com/national"><strong>জাতীয়</strong></a></p>

                            <h1 class="detailHeadline marginT0"><strong>নির্বাচন ১৫ ফেব্রুয়ারির আগে হবে, কোনও শক্তি নেই এটাকে পেছানোর: শফিকুল আলম</strong></h1>
                            <p class="borderC1T1 marginB0"></p>

                            <div class="marginT10">
                                <div class="detailAuthorDiv">
                                    <img src="https://www.bhorerkagoj.com/uploads/settings/icon.webp" class="img-responsive borderRadius50P" width="35" alt="Icon">
                                </div>
                                <div class="displayInlineBlock">
                                    <p class="detailReporter">কাগজ ডেস্ক</p>
                                    <p class="detailPTime color1">প্রকাশ: ৩১ অক্টোবর ২০২৫, ০৩:৩৮ পিএম</p>
                                </div>
                            </div>
                            <!-- sharethis -->
                            <div class="marginT15 hidden-print">
                                <div class="sharethis-inline-share-buttons st-left" style="text-align: left !important;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- video section -->

                    <!-- photo section -->
                    <div class="col-sm-12 col-md-12 detailPhotoDiv">
                        <div class="detailPhoto">
                            <figure>
                                <img src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/shafiq-6904838c9f43a.jpg" class="img-responsive" alt="নির্বাচন ১৫ ফেব্রুয়ারির আগে হবে, কোনও শক্তি নেই এটাকে পেছানোর: শফিকুল আলম">
                            </figure>
                            <figcaption>
                                <p>ছবি: সংগৃহীত</p>
                            </figcaption>
                        </div>
                    </div>


                    <!-- video tag unibots -->
                    <!--<div class="col-sm-12 col-md-12 paddingLR20 hidden-print">
                        <div class="text-center overflowHidden marginB20 w300 adDiv">
                            <div id="div-ub-bhorerkagoj2.com_1729159526012">
                                    <script>
                                           window.unibots = window.unibots || { cmd: [] };
                                           unibots.cmd.push(function() { unibotsPlayer("bhorerkagoj2.com_1729159526012") });
                                   </script>
                            </div>
                        </div>
                    </div>-->


                    <!-- body section -->
                    <div class="col-sm-12 col-md-12 paddingLR20">
                        <!-- news body -->
                        <div class="detailBody"><p>গণভোট ইস্যুতে প্রধান উপদেষ্টা সিদ্ধান্ত নেবেন। যে সিদ্ধান্তই নেওয়া হোক না কেন, নির্বাচন ১৫ ফেব্রুয়ারির আগে হবে। কোনও শক্তি এটিকে পেছাতে পারবে না। প্রধান উপদেষ্টার প্রেস সচিব শফিকুল আলম এসব কথা বলেছেন।</p><p>শুক্রবার (৩১ অক্টোবর) দুপুরে নোয়াখালী বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়ে (নোবিপ্রবি) জুলাই কন্যা ফাউন্ডেশনের উদ্যোগে আয়োজিত অনুষ্ঠানের সমাপনী ও পুরস্কার বিতরণ শেষে এ কথা বলেন তিনি।</p><p>শফিকুল আলম আরও বলেন, বিভিন্ন রাজনৈতিক দল তাদের মত জানিয়েছে। আমরা এটিকে হুমকি হিসেবে দেখছি না। যেটা সবচেয়ে উত্তম প্রধান উপদেষ্টা সেটাই করবেন। আগামী ১৩ নভেম্বর আদালত শেখ হাসিনার বিচারের দিন জানাবেন।</p><p>চব্বিশের গণঅভ্যুত্থানে নারীর ভূমিকা উল্লেখ করে তিনি বলেন, স্বৈরাচার পতন ও গণঅভ্যুত্থানে পুরুষ ও নারীরা রাজপথে কাঁধে কাঁধ মিলিয়ে আন্দোলন করেছে। এখন নারীরা পিছিয়ে নেই। সব ক্ষেত্রেই তারা প্রতিনিধিত্ব করছে।</p><p>উল্লেখ্য, তরুণদের জ্ঞান ও মেধার বিকাশকে উৎসাহিত করার লক্ষ্যে নোয়াখালী বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়ে জুলাই কন্যা ফাউন্ডেশনের উদ্যোগে 'মাইন্ডব্রিজ ও নলেজ কম্পিটিশন ২০২৫' এর আয়োজন করা হয়।</p><p>নোবিপ্রবি কেন্দ্রীয় অডিটোরিয়ামে ফাউন্ডেশনের সভাপতি জান্নাতুল নাঈম প্রমির সভাপতিত্বে এই প্রতিযোগিতা অনুষ্ঠিত হয়।</p><p>প্রধান পৃষ্ঠপোষক হিসেবে উপস্থিত ছিলেন নোবিপ্রবির উপাচার্য অধ্যাপক ড. মোহাম্মদ ইসমাইল। বিশেষ অতিথি ছিলেন নোয়াখালীর অতিরিক্ত জেলা প্রশাসক ইসমাঈল হোসেন।</p></div>

                        <!-- news document -->

                        <!-- news tag -->
                        <div class="detailTag hidden-print">
                            <p>
                                <a class="tagItem" aria-label="প্রেস সচিব " href="https://www.bhorerkagoj.com/topic/প্রেস-সচিব">প্রেস সচিব </a>
                                <a class="tagItem" aria-label="শফিকুল আলম" href="https://www.bhorerkagoj.com/topic/শফিকুল-আলম">শফিকুল আলম</a>
                                <a class="tagItem" aria-label="গণভোট" href="https://www.bhorerkagoj.com/topic/গণভোট">গণভোট</a>
                            </p>
                        </div>

                        <!-- ad rits -->
                        <div class="adDiv w300 borderRadius5 overflowHidden marginT20 hidden-print">

                        </div>
                        <!-- ad -->

                        <!-- load second detail -->
                        <div class="loadAjaxDetail1"></div>

                    </div>


                    <!-- timeline news -->

                    <!-- subscribe & follow -->
                    <div class="col-sm-12 col-md-12 marginT40 paddingLR20 hidden-print">
                        <h2 class="sectionTitle marginB15">সাবস্ক্রাইব ও অনুসরণ করুন <span class="bottomMarked"></span></h2>
                        <div class="row marginLR-10 desktopFlexRow">
                            <div class="col-xs-12 paddingLR10">
                                <div style="border: 1px solid #ebedf0;height: 100%;padding: 8px 8px 0px 8px">
                                    <script src="https://apis.google.com/js/platform.js"></script>
                                    <div class="g-ytsubscribe" data-channelid="UCXs9EVaxc1rQjTM9Ber2pAg" data-layout="full" data-count="hidden"></div>
                                </div>
                            </div>
                            <div class="col-xs-12 paddingLR10">
                                <div class="fb-page" data-href="https://www.facebook.com/DailyBhorerKagoj/" data-tabs="" data-width="" data-height="" data-small-header="true" data-adapt-container-width="true" data-hide-cover="true" data-show-facepile="false"><blockquote cite="https://www.facebook.com/DailyBhorerKagoj/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/DailyBhorerKagoj/">Bhorer Kagoj</a></blockquote></div>
                            </div>
                        </div>
                    </div>

                    <!-- taboola ad -->
                    <!--<div class="col-sm-12 col-md-12 marginT40 paddingLR20 hidden-print">
                        <div class="taboola-ad-container1"></div>
                    </div>-->


                    <!-- related news -->
                    <!-- <div class="col-sm-12 col-md-12 marginT40 paddingLR20 hidden-print relatedNewsWidgetMobile1" style="display: none;">
                        <h2 class="sectionTitle marginB15">সম্পর্কিত খবর <span class="bottomMarked"></span></h2>
                        <div class="row desktopFlexRow paddingLR5 ajaxRelatedNewsDivMobile1"></div>
                    </div> -->

                    <!-- ad -->
                    <div class="col-sm-12 col-md-12 marginT40 paddingLR20 hidden-print">
                        <div class="adDiv w300 borderRadius5 overflowHidden">
                            <ins class="adsbygoogle"
                                 style="display:inline-block;width:300px;height:250px"
                                 data-ad-client="ca-pub-6140828263407584"
                                 data-ad-slot="9190197910"></ins>
                            <script>
                                (adsbygoogle = window.adsbygoogle || []).push({});
                            </script>
                        </div>
                    </div>
                    <!-- ad -->


                    <!-- special news -->

                    <!-- category news -->
                    <div class="col-sm-12 col-md-12 marginT40 paddingLR20 hidden-print categoryNewsWidgetMobile1" style="display: none;">
                        <h2 class="sectionTitle marginB10">জাতীয় থেকে আরো <span class="bottomMarked"></span></h2>
                        <div class="sectionListMedia ajaxCategoryNewsDivMobile1"></div>
                    </div>

                    <!-- latest popular news -->
                    <div class="col-sm-12 col-md-12 marginT50 paddingLR20 hidden-print popularNewsWidgetMobile">
                        <div class="hidden-xs borderRadius5 borderC1-1">
                            <div class="tabNews bgWhiteImp height100P width100P">
                                <ul class="nav nav-tabs borderTRadius5" role="tablist">
                                    <li role="presentation" class="text-center borderNone active"><a aria-label="সর্বশেষ" class="" href="#latestTab" aria-controls="highestTab" role="tab" data-toggle="tab" aria-expanded="false">সর্বশেষ</a></li>
                                    <li role="presentation" class="text-center borderNone"><a aria-label="পঠিত" class="" href="#highestTab" aria-controls="highestTab" role="tab" data-toggle="tab" aria-expanded="false">সর্বাধিক পঠিত</a></li>
                                </ul>

                                <div class="tab-content borderBRadius5 borderT0">
                                    <div role="tabpanel" class="tab-pane active" id="latestTab">
                                        <div class="scrollbar1 latestDivHeight">
                                            <div class="desktopSectionListMedia listItemLastBB0">
                                                <div class="media positionRelative paddingLR10">
                                                    <div class="media-left paddingR5">
                                                        <div class="positionRelative">
                                                            <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/omra-690489d504dfd.jpg" width="80" alt="ওমরাহ ভিসার মেয়াদ কমালো সৌদি আরব">
                                                        </div>
                                                    </div>
                                                    <div class="media-body">
                                                        <h4 class="margin0 marginL5 hoverBlue title12">ওমরাহ ভিসার মেয়াদ কমালো সৌদি আরব</h4>
                                                    </div>
                                                    <a aria-label="ওমরাহ ভিসার মেয়াদ কমালো সৌদি আরব" href="https://www.bhorerkagoj.com/international/775198" class="linkOverlay"></a>
                                                </div>
                                                <div class="media positionRelative paddingLR10">
                                                    <div class="media-left paddingR5">
                                                        <div class="positionRelative">
                                                            <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/cultivate-hen-bk-690485bd6d577.jpg" width="80" alt="সবজি-মুরগিতে স্বস্তি, বেড়েছে আটার দাম">
                                                        </div>
                                                    </div>
                                                    <div class="media-body">
                                                        <h4 class="margin0 marginL5 hoverBlue title12">সবজি-মুরগিতে স্বস্তি, বেড়েছে আটার দাম</h4>
                                                    </div>
                                                    <a aria-label="সবজি-মুরগিতে স্বস্তি, বেড়েছে আটার দাম" href="https://www.bhorerkagoj.com/economics/775197" class="linkOverlay"></a>
                                                </div>
                                                <div class="media positionRelative paddingLR10">
                                                    <div class="media-left paddingR5">
                                                        <div class="positionRelative">
                                                            <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/shafiq-6904838c9f43a.jpg" width="80" alt="নির্বাচন ১৫ ফেব্রুয়ারির আগে হবে, কোনও শক্তি নেই এটাকে পেছানোর: শফিকুল আলম">
                                                        </div>
                                                    </div>
                                                    <div class="media-body">
                                                        <h4 class="margin0 marginL5 hoverBlue title12">নির্বাচন ১৫ ফেব্রুয়ারির আগে হবে, কোনও শক্তি নেই এটাকে পেছানোর: শফিকুল আলম</h4>
                                                    </div>
                                                    <a aria-label="নির্বাচন ১৫ ফেব্রুয়ারির আগে হবে, কোনও শক্তি নেই এটাকে পেছানোর: শফিকুল আলম" href="https://www.bhorerkagoj.com/national/775196" class="linkOverlay"></a>
                                                </div>
                                                <div class="media positionRelative paddingLR10">
                                                    <div class="media-left paddingR5">
                                                        <div class="positionRelative">
                                                            <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/fakrul-6904811e26949.jpg" width="80" alt="অন্তর্বর্তী সরকার জনগণের সঙ্গে বিশ্বাসঘাতকতা করেছে: মির্জা ফখরুল">
                                                        </div>
                                                    </div>
                                                    <div class="media-body">
                                                        <h4 class="margin0 marginL5 hoverBlue title12">অন্তর্বর্তী সরকার জনগণের সঙ্গে বিশ্বাসঘাতকতা করেছে: মির্জা ফখরুল</h4>
                                                    </div>
                                                    <a aria-label="অন্তর্বর্তী সরকার জনগণের সঙ্গে বিশ্বাসঘাতকতা করেছে: মির্জা ফখরুল" href="https://www.bhorerkagoj.com/politics/775195" class="linkOverlay"></a>
                                                </div>
                                                <div class="media positionRelative paddingLR10">
                                                    <div class="media-left paddingR5">
                                                        <div class="positionRelative">
                                                            <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/Sorok-69038f42cbacf.jpg" width="80" alt="ব্র্যাক ড্রাইভিং স্কুলের সড়ক নিরাপত্তা বিষয়ক সচেতনতামূলক মানববন্ধন">
                                                        </div>
                                                    </div>
                                                    <div class="media-body">
                                                        <h4 class="margin0 marginL5 hoverBlue title12">ব্র্যাক ড্রাইভিং স্কুলের সড়ক নিরাপত্তা বিষয়ক সচেতনতামূলক মানববন্ধন</h4>
                                                    </div>
                                                    <a aria-label="ব্র্যাক ড্রাইভিং স্কুলের সড়ক নিরাপত্তা বিষয়ক সচেতনতামূলক মানববন্ধন" href="https://www.bhorerkagoj.com/national-other/775194" class="linkOverlay"></a>
                                                </div>
                                                <div class="media positionRelative paddingLR10">
                                                    <div class="media-left paddingR5">
                                                        <div class="positionRelative">
                                                            <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/Medical-Tour-69038ee721867.jpg" width="80" alt="মেডিকেল ট্যুরিজমে চমক এনেছে সুহা ট্রাভেলস থাইল্যান্ড">
                                                        </div>
                                                    </div>
                                                    <div class="media-body">
                                                        <h4 class="margin0 marginL5 hoverBlue title12">মেডিকেল ট্যুরিজমে চমক এনেছে সুহা ট্রাভেলস থাইল্যান্ড</h4>
                                                    </div>
                                                    <a aria-label="মেডিকেল ট্যুরিজমে চমক এনেছে সুহা ট্রাভেলস থাইল্যান্ড" href="https://www.bhorerkagoj.com/tourism/775193" class="linkOverlay"></a>
                                                </div>
                                                <div class="media positionRelative paddingLR10">
                                                    <div class="media-left paddingR5">
                                                        <div class="positionRelative">
                                                            <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/JCI-690388dbe515a.jpg" width="80" alt="সিংগাইরে মাদ্রাসায় বসার বেঞ্চ-টুল উপহার দিলো জেসিআই মানিকগঞ্জ">
                                                        </div>
                                                    </div>
                                                    <div class="media-body">
                                                        <h4 class="margin0 marginL5 hoverBlue title12">সিংগাইরে মাদ্রাসায় বসার বেঞ্চ-টুল উপহার দিলো জেসিআই মানিকগঞ্জ</h4>
                                                    </div>
                                                    <a aria-label="সিংগাইরে মাদ্রাসায় বসার বেঞ্চ-টুল উপহার দিলো জেসিআই মানিকগঞ্জ" href="https://www.bhorerkagoj.com/corporate/775192" class="linkOverlay"></a>
                                                </div>
                                                <div class="media positionRelative paddingLR10">
                                                    <div class="media-left paddingR5">
                                                        <div class="positionRelative">
                                                            <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/Rahman-Mridha-Bangladesh-690382e533377.jpg" width="80" alt="স্বাধীনতার এক নির্মম পরিহাস">
                                                        </div>
                                                    </div>
                                                    <div class="media-body">
                                                        <h4 class="margin0 marginL5 hoverBlue title12">স্বাধীনতার এক নির্মম পরিহাস</h4>
                                                    </div>
                                                    <a aria-label="স্বাধীনতার এক নির্মম পরিহাস" href="https://www.bhorerkagoj.com/thinking/775190" class="linkOverlay"></a>
                                                </div>
                                                <div class="media positionRelative paddingLR10">
                                                    <div class="media-left paddingR5">
                                                        <div class="positionRelative">
                                                            <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/Dr-Yunus-690381dc2343d.jpg" width="80" alt="ড. ইউনূস হতে পারেন জাতিসংঘের মহাসচিব!">
                                                        </div>
                                                    </div>
                                                    <div class="media-body">
                                                        <h4 class="margin0 marginL5 hoverBlue title12">ড. ইউনূস হতে পারেন জাতিসংঘের মহাসচিব!</h4>
                                                    </div>
                                                    <a aria-label="ড. ইউনূস হতে পারেন জাতিসংঘের মহাসচিব!" href="https://www.bhorerkagoj.com/thinking/775189" class="linkOverlay"></a>
                                                </div>
                                                <div class="media positionRelative paddingLR10">
                                                    <div class="media-left paddingR5">
                                                        <div class="positionRelative">
                                                            <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/Legal-advisor-6903644b430a2.jpg" width="80" alt="গণভোটের বিষয়ে চূড়ান্ত সিদ্ধান্ত দেবেন প্রধান উপদেষ্টা : আইন উপদেষ্টা">
                                                        </div>
                                                    </div>
                                                    <div class="media-body">
                                                        <h4 class="margin0 marginL5 hoverBlue title12">গণভোটের বিষয়ে চূড়ান্ত সিদ্ধান্ত দেবেন প্রধান উপদেষ্টা : আইন উপদেষ্টা</h4>
                                                    </div>
                                                    <a aria-label="গণভোটের বিষয়ে চূড়ান্ত সিদ্ধান্ত দেবেন প্রধান উপদেষ্টা : আইন উপদেষ্টা" href="https://www.bhorerkagoj.com/government/775188" class="linkOverlay"></a>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="bg2 title11 padding10 text-center marginB0 borderBRadius5 shadow1"><a class="textDecorationNone colorBlack hoverBlue" href="https://www.bhorerkagoj.com/latest">সব খবর</a></p>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="highestTab">
                                        <div class="scrollbar1 latestDivHeight">
                                            <div class="desktopSectionListMedia listItemLastBB0 ajaxPopularNewsDivDesktop"><div class="popularNewsLoaderDiv text-center marginT50"><i class="fa fa-spinner"></i></div></div>
                                        </div>
                                        <p class="bg2 title11 padding10 text-center marginB0 borderBRadius5 shadow1"><a class="textDecorationNone colorBlack hoverBlue" href="https://www.bhorerkagoj.com/popular">সব খবর</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="visible-xs borderRadius5 borderC1-1">
                            <div class="tabNews bgWhiteImp height100P width100P">
                                <ul class="nav nav-tabs borderTRadius5" role="tablist">
                                    <li role="presentation" class="text-center borderNone active"><a aria-label="সর্বশেষ" class="" href="#latestTab" aria-controls="latestTab" role="tab" data-toggle="tab" aria-expanded="false">সর্বশেষ</a></li>
                                    <li role="presentation" class="text-center borderNone"><a aria-label="পঠিত" class="" href="#highestTab" aria-controls="highestTab" role="tab" data-toggle="tab" aria-expanded="false">সর্বাধিক পঠিত</a></li>
                                </ul>

                                <div class="tab-content borderBRadius5 borderT0">
                                    <div role="tabpanel" class="tab-pane active" id="latestTab">
                                        <div class="scrollbar1">
                                            <div class="sectionListMedia listItemLastBB0">
                                                <div class="media positionRelative paddingLR10">
                                                    <div class="media-left paddingR5">
                                                        <div class="positionRelative">
                                                            <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/omra-690489d504dfd.jpg" width="140" alt="ওমরাহ ভিসার মেয়াদ কমালো সৌদি আরব">
                                                        </div>
                                                    </div>
                                                    <div class="media-body">
                                                        <h4 class="margin0 marginL5 hoverBlue">ওমরাহ ভিসার মেয়াদ কমালো সৌদি আরব</h4>
                                                    </div>
                                                    <a aria-label="ওমরাহ ভিসার মেয়াদ কমালো সৌদি আরব" href="https://www.bhorerkagoj.com/international/775198" class="linkOverlay"></a>
                                                </div>
                                                <div class="media positionRelative paddingLR10">
                                                    <div class="media-left paddingR5">
                                                        <div class="positionRelative">
                                                            <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/cultivate-hen-bk-690485bd6d577.jpg" width="140" alt="সবজি-মুরগিতে স্বস্তি, বেড়েছে আটার দাম">
                                                        </div>
                                                    </div>
                                                    <div class="media-body">
                                                        <h4 class="margin0 marginL5 hoverBlue">সবজি-মুরগিতে স্বস্তি, বেড়েছে আটার দাম</h4>
                                                    </div>
                                                    <a aria-label="সবজি-মুরগিতে স্বস্তি, বেড়েছে আটার দাম" href="https://www.bhorerkagoj.com/economics/775197" class="linkOverlay"></a>
                                                </div>
                                                <div class="media positionRelative paddingLR10">
                                                    <div class="media-left paddingR5">
                                                        <div class="positionRelative">
                                                            <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/shafiq-6904838c9f43a.jpg" width="140" alt="নির্বাচন ১৫ ফেব্রুয়ারির আগে হবে, কোনও শক্তি নেই এটাকে পেছানোর: শফিকুল আলম">
                                                        </div>
                                                    </div>
                                                    <div class="media-body">
                                                        <h4 class="margin0 marginL5 hoverBlue">নির্বাচন ১৫ ফেব্রুয়ারির আগে হবে, কোনও শক্তি নেই এটাকে পেছানোর: শফিকুল আলম</h4>
                                                    </div>
                                                    <a aria-label="নির্বাচন ১৫ ফেব্রুয়ারির আগে হবে, কোনও শক্তি নেই এটাকে পেছানোর: শফিকুল আলম" href="https://www.bhorerkagoj.com/national/775196" class="linkOverlay"></a>
                                                </div>
                                                <div class="media positionRelative paddingLR10">
                                                    <div class="media-left paddingR5">
                                                        <div class="positionRelative">
                                                            <img class="media-object borderRadius5" src="https://www.bhorerkagoj.com/uploads/2025/10/online/photos/small/fakrul-6904811e26949.jpg" width="140" alt="অন্তর্বর্তী সরকার জনগণের সঙ্গে বিশ্বাসঘাতকতা করেছে: মির্জা ফখরুল">
                                                        </div>
                                                    </div>
                                                    <div class="media-body">
                                                        <h4 class="margin0 marginL5 hoverBlue">অন্তর্বর্তী সরকার জনগণের সঙ্গে বিশ্বাসঘাতকতা করেছে: মির্জা ফখরুল</h4>
                                                    </div>
                                                    <a aria-label="অন্তর্বর্তী সরকার জনগণের সঙ্গে বিশ্বাসঘাতকতা করেছে: মির্জা ফখরুল" href="https://www.bhorerkagoj.com/politics/775195" class="linkOverlay"></a>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="bg2 title12 padding10 text-center marginB0 borderBRadius5 shadow1"><a class="textDecorationNone colorBlack hoverBlue" href="https://www.bhorerkagoj.com/latest">সব খবর</a></p>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="highestTab">
                                        <div class="scrollbar1">
                                            <div class="sectionListMedia listItemLastBB0 ajaxPopularNewsDivMobile"><div class="popularNewsLoaderDiv text-center marginT50"><i class="fa fa-spinner"></i></div></div>
                                        </div>
                                        <p class="bg2 title12 padding10 text-center marginB0 borderBRadius5 shadow1"><a class="textDecorationNone colorBlack hoverBlue" href="https://www.bhorerkagoj.com/popular">সব খবর</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- latest popular news end -->

                </div>
            </div>

            <!-- article reached -->
            <div class="articleReached1" data-url="https://www.bhorerkagoj.net/national/775196"></div>

            <!-- append second detail -->
            <div class="appendAjaxDetail1 hidden-print"><p class="text-center marginB0"><i class="fa fa-spinner"></i></p></div>

        </div>
        <!-- mobile version end -->


        <!-- fullscreen ad -->


        <!-- ad rits -->
        <div class="mInarticle1" style="display: none;">
            <div class="futureads" style="width:300px;height:250px;display:inline-block;" data-ad-slot="pw_42033"></div> <script type="text/javascript">(wapTag.Init = window.wapTag.Init || []).push(function () { wAPITag.display("pw_42033") })</script>
        </div>
        <div class="mInarticle2" style="display: none;">
            <div class="futureads" style="width:300px;height:250px;display:inline-block;" data-ad-slot="pw_42580"></div> <script type="text/javascript">(wapTag.Init = window.wapTag.Init || []).push(function () { wAPITag.display("pw_42580") })</script>
        </div>

        <!-- ad rits -->
        <div class="dInarticle1" style="display: none;">
            <div class="futureads" style="width:728px;height:90px;display:inline-block;" data-ad-slot="pw_42109"></div> <script type="text/javascript">(wapTag.Init = window.wapTag.Init || []).push(function () { wAPITag.display("pw_42109") })</script>
        </div>

    </div>
    <!-- main div end -->
@endsection

@push('meta')
    @php
        $logoPath = $settings?->site_logo;
        $logoUrl = $logoPath
            ? (\Illuminate\Support\Str::startsWith($logoPath, ['http://', 'https://'])
                ? $logoPath
                : \Illuminate\Support\Facades\Storage::url($logoPath))
            : asset('favicon.ico');

        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $post->title,
            'datePublished' => $seo['published_time'] ?? optional($post->created_at)->toIso8601String(),
            'dateModified' => $seo['modified_time'] ?? optional($post->updated_at ?? $post->created_at)->toIso8601String(),
            'author' => $post->author ? [
                '@type' => 'Person',
                'name' => $post->author->name,
            ] : null,
            'publisher' => [
                '@type' => 'Organization',
                'name' => $settings->site_title ?? config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $logoUrl,
                ],
            ],
            'image' => array_filter([$seo['image'] ?? null]),
            'mainEntityOfPage' => $seo['canonical'] ?? route('posts.show', $post),
            'articleSection' => $post->category?->name,
            'description' => $seo['description'] ?? $post->excerpt,
        ];

        $articleSchema = array_filter($articleSchema, fn ($value) => !is_null($value));

        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => route('home'),
                ],
            ],
        ];

        if ($post->category) {
            $breadcrumbSchema['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $post->category->name,
            ];
            $breadcrumbSchema['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $post->title,
                'item' => $seo['canonical'] ?? route('posts.show', $post),
            ];
        } else {
            $breadcrumbSchema['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $post->title,
                'item' => $seo['canonical'] ?? route('posts.show', $post),
            ];
        }
    @endphp
    <script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
