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
