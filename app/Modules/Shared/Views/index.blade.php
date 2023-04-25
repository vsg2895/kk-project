@extends('shared::layouts.default') @section('pageTitle', 'Körkortsjakten - Sveriges trafikskolor |') @section('content')
    <div id="landing" class="container">


        <div id="landing-hero" class="container-fluid">
            <div class="hidden-md-down klarna-landing" style="position: absolute; margin-top: 8rem; margin-left: 80%; "><svg viewBox="0 0 45 25" height="65" width="105" xmlns="http://www.w3.org/2000/svg" class="ico-md"><title>Klarna Payment Badge</title><g fill="none"><rect width="45" height="25" rx="4.321" fill="#FFB3C7"></rect><path d="M40.794 14.646a1.07 1.07 0 0 0-1.066 1.076 1.07 1.07 0 0 0 1.066 1.076c.588 0 1.066-.482 1.066-1.076a1.07 1.07 0 0 0-1.066-1.076zm-3.508-.831c0-.814-.689-1.473-1.539-1.473s-1.539.66-1.539 1.473c0 .813.69 1.472 1.54 1.472s1.538-.659 1.538-1.472zm.006-2.863h1.698v5.725h-1.698v-.366a2.96 2.96 0 0 1-1.684.524c-1.653 0-2.993-1.352-2.993-3.02s1.34-3.02 2.993-3.02c.625 0 1.204.193 1.684.524v-.367zm-13.592.746v-.745h-1.739v5.724h1.743v-2.673c0-.902.968-1.386 1.64-1.386h.02v-1.665c-.69 0-1.323.298-1.664.745zm-4.332 2.117c0-.814-.689-1.473-1.539-1.473s-1.539.66-1.539 1.473c0 .813.69 1.472 1.54 1.472.85 0 1.538-.659 1.538-1.472zm.006-2.863h1.699v5.725h-1.699v-.366c-.48.33-1.059.524-1.684.524-1.653 0-2.993-1.352-2.993-3.02s1.34-3.02 2.993-3.02c.625 0 1.204.193 1.684.524v-.367zm10.223-.153c-.678 0-1.32.212-1.75.798v-.644h-1.691v5.724h1.712v-3.008c0-.87.578-1.297 1.275-1.297.746 0 1.176.45 1.176 1.285v3.02h1.696v-3.64c0-1.332-1.05-2.238-2.418-2.238zm-17.374 5.878h1.778V8.402h-1.778v8.275zm-7.81.002h1.883V8.4H4.414v8.279zM10.999 8.4c0 1.792-.692 3.46-1.926 4.699l2.602 3.58H9.35l-2.827-3.89.73-.552A4.768 4.768 0 0 0 9.155 8.4h1.842z" fill="#0A0B09"></path></g></svg></div>
            <div class="hidden-md-up klarna-landing" style="position: absolute; margin-top: -2.1rem; margin-left: 18rem; z-index: 4"><svg viewBox="0 0 45 25" height="45" width="45" xmlns="http://www.w3.org/2000/svg" class="ico-md"><title>Klarna Payment Badge</title><g fill="none"><rect width="45" height="25" rx="4.321" fill="#FFB3C7"></rect><path d="M40.794 14.646a1.07 1.07 0 0 0-1.066 1.076 1.07 1.07 0 0 0 1.066 1.076c.588 0 1.066-.482 1.066-1.076a1.07 1.07 0 0 0-1.066-1.076zm-3.508-.831c0-.814-.689-1.473-1.539-1.473s-1.539.66-1.539 1.473c0 .813.69 1.472 1.54 1.472s1.538-.659 1.538-1.472zm.006-2.863h1.698v5.725h-1.698v-.366a2.96 2.96 0 0 1-1.684.524c-1.653 0-2.993-1.352-2.993-3.02s1.34-3.02 2.993-3.02c.625 0 1.204.193 1.684.524v-.367zm-13.592.746v-.745h-1.739v5.724h1.743v-2.673c0-.902.968-1.386 1.64-1.386h.02v-1.665c-.69 0-1.323.298-1.664.745zm-4.332 2.117c0-.814-.689-1.473-1.539-1.473s-1.539.66-1.539 1.473c0 .813.69 1.472 1.54 1.472.85 0 1.538-.659 1.538-1.472zm.006-2.863h1.699v5.725h-1.699v-.366c-.48.33-1.059.524-1.684.524-1.653 0-2.993-1.352-2.993-3.02s1.34-3.02 2.993-3.02c.625 0 1.204.193 1.684.524v-.367zm10.223-.153c-.678 0-1.32.212-1.75.798v-.644h-1.691v5.724h1.712v-3.008c0-.87.578-1.297 1.275-1.297.746 0 1.176.45 1.176 1.285v3.02h1.696v-3.64c0-1.332-1.05-2.238-2.418-2.238zm-17.374 5.878h1.778V8.402h-1.778v8.275zm-7.81.002h1.883V8.4H4.414v8.279zM10.999 8.4c0 1.792-.692 3.46-1.926 4.699l2.602 3.58H9.35l-2.827-3.89.73-.552A4.768 4.768 0 0 0 9.155 8.4h1.842z" fill="#0A0B09"></path></g></svg></div>

            <h1 class="slogan">Sveriges största mötesplats för elever och trafikskolor</h1>
{{--            <div class="usp-bubbles usp-bubble usp-student" onclick="window.location = '/presentkort'"--}}
{{--                 style="cursor: pointer !important;">--}}
{{--                <div class="usp-title">--}}
{{--                    <div class="h2">--}}
{{--                        Vi sponsrar med 20% extra på presentkortet--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <!-- @icon('snowflake-2') @icon('snowflake-3') -->--}}

{{--                --}}{{--@if($xmasCampaign)--}}
{{--                --}}{{--<a class="usp-bubble usp-xmas" href="{{ route('shared::gift_card.index') }}">--}}
{{--                --}}{{--<div class="usp-title">--}}
{{--                --}}{{--<div class="h2">Årets</div>--}}
{{--                --}}{{--<div class="h1 bold">Present</div>--}}
{{--                --}}{{--<div class="h2">Köp ett presentkort få {{ $bonus }}% extra</div>--}}
{{--                --}}{{--</div>--}}
{{--                --}}{{--<!-- @icon('snowflake-2') @icon('snowflake-3') -->--}}
{{--                --}}{{--</a>--}}
{{--                --}}{{--@else--}}
{{--                --}}{{--<a class="usp-bubble usp-organization" href="{{ route('auth::register.organization') }}">--}}
{{--                --}}{{--<div class="usp-title">--}}
{{--                --}}{{--<div class="h2">Anslut din trafikskola</div>--}}
{{--                --}}{{--</div>--}}
{{--                --}}{{--</a>--}}
{{--                --}}{{--@endif--}}
{{--            </div>--}}
        </div>

        <div class="landing-search-bar container">
            <form class="card clearfix" method="POST" action="{{ route('shared::index.search') }}">
                {{ csrf_field() }}

                <div class="landing-search-vehicle col">
                    <label class="h4">Jag vill lära mig köra</label>
                    <div class="search-filter-vehicle">
                        @foreach($vehicles as $indexKey => $vehicle)

                            <span>
                            <div class="form-checkbutton">
                                <input id="{{ $vehicle->label }}"
                                       {{ $indexKey==0 ? 'checked' : '' }} value="{{ $vehicle->id }}" type="radio"
                                       name="vehicle_id"
                                />
                                <label for="{{ $vehicle->label }}">
                                    <div class="vehicle-label tag tag-pill tag-default">{{ $vehicle->identifier }}</div>
                                    @icon(strtolower($vehicle->name), 'md')
                                </label>
                            </div>

                  </span>
                        @endforeach
                    </div>
                </div>

                <div id="landing-search-city" class="col">
                    <label class="h4">i närheten av ...</label>
                    <semantic-dropdown :search="true" id="cities" placeholder="Välj stad" form-name="city_id"
                                       :data="{{ $cities }}">
                        <template slot="dropdown-item" slot-scope="props">
                            <div class="item" :data-value="props.item.id">
                                <div class="item-text">@{{ props.item.name }}</div>
                            </div>
                        </template>
                    </semantic-dropdown>
                </div>

                <div id="btn-search" class="col">
                    <button type="submit" class="btn btn-lg btn-primary find-schools-btn-landing">Hitta trafikskolor!
                    </button>
                </div>
            </form>
        </div>

        @if($latestCourses->count()
        < 0)
            <div id="latest" class="container">
                <h2>Senast bokade kurser</h2>
                <div class="row">
                    @foreach($latestCourses as $course)
                        <div class="col-sm-6 col-lg-4">
                            <div class="course-card card card-block">
                                @icon(strtolower($course->segment->vehicle->name), 'md')
                                <div class="type tag tag-pill tag-default">{{ $course->segment->label }}</div>
                                <div class="date">{{ $course->start_time->formatLocalized('%A %d:e %B, %Y') }}</div>
                                <div class="time text-numerical">{{ $course->start_hour }}
                                    - {{ $course->end_hour }}</div>
                                <div class="price text-numerical">från {{ $course->price_with_currency }}</div>

                                <a href="{{ route('shared::courses.show', ['citySlug' => $course->school->city->slug, 'schoolSlug' => $course->school->slug, 'courseId' => $course->id]) }}"
                                   class="btn btn-outline-primary hidden-xs-down book-course-btn-latest-landing">Boka
                                    nu</a>
                                <a class="school-link"
                                   href="{{ route('shared::schools.show', ['citySlug' => $course->school->city->slug, 'schoolSlug' => $course->school->slug]) }}">
                                    @if($course->school->logo)
                                        <img class="school-logo"
                                             src="{{ $course->school->logo->version('small')->path }}"
                                             alt="Logo"> @elseif($course->school->organization->logo)
                                        <img class="school-logo"
                                             src="{{ $course->school->organization->logo->version('small')->path }}"
                                             alt="Logo"> @else
                                        <span class="school-name h4">{{ $course->school->name }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('shared::courses.show', ['citySlug' => $course->school->city->slug, 'schoolSlug' => $course->school->slug, 'courseId' => $course->id]) }}"
                                   class="btn btn-outline-primary hidden-sm-up book-course-btn-latest-landing">Boka
                                    nu</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif @if($xmasCampaign)
            <div id="xmas-gift-card">
                <div class="container text-xs-center mb-2">
                    <div class="row">
                        @include('elements.landing-block', ['col' => 4, 'check' => 1, 'title' => 'INTRODUKTIONSKURS','image' => '/images/fl_1_intro.jpg', 'content' => 'Kurs för handledare och du som ska övningsköra . Hitta första, bästa, kurstillfälle i din stad. Boka och betala - snabbt, smidigt och säkert med Klarna.', 'route' => '/introduktionskurser', 'btnText' => 'Till kurserna'])
                        @include('elements.landing-block', ['col' => 4, 'mtMobile' => true, 'check' => 2, 'title' => 'RISKUTBILDNING DEL <span class="upper-number">1</span>','image' => '/images/fl_2.png', 'content' => 'Även kallad Riskettan. Hitta första, bästa,  kurstillfälle i din stad.  Boka och betala - <b>snabbt, smidigt och säkert</b> med Klarna.', 'route' => route('shared::riskettan'), 'btnText' => 'Till kurserna'])
                        @include('elements.landing-block', ['col' => 4, 'mtMobile' => true, 'check' => 3, 'title' => 'RISKTVÅAN (HALKAN)','image' => '/images/car.jpg', 'content' => 'I slutet av utbildningen för B-körkort är det dags att göra del två av den obligatoriska riskutbildningen (tidigare kallad halkutbildning). Den måste vara genomförd och giltig innan teori- och körprovet genomförs.', 'route' => '/risktvaan', 'btnText' => 'Till kurserna'])
                    </div>
                </div>
            </div>
            <div id="xmas-gift-card">
                <div class="container text-xs-center mb-2">
                    <div class="row">
                        @include('elements.landing-block', ['col' => 6, 'check' => 4, 'title' => 'KÖRKORTSTEORI OCH TESTPROV','image' => '/images/fl_1_prov_new.jpg', 'content' => 'Här får du tillgång till alla körkortsfrågor du behöver kunna för att klara teoriprovet. Alla körkortsfrågor har en förklaring som snabbt hjälper dig att förstå även det svåraste frågorna. Studera och testa dig när du vill, obegränsat antal prov.', 'route' => '/teoriprov-online', 'btnText' => 'Till kurserna'])
                        @include('elements.landing-block', ['col' => 6, 'mtMobile' => true, 'check' => 5, 'title' => 'YKB Utbildningar ','image' => '/images/sub/ykb_35_h.jpeg', 'content' => 'YKB utbildning är ett lagkrav för chaufförer som kör lastbil alt. buss i yrkestrafik. Vart femte år behöver YKB beviset uppdateras med fortbildning för transportslagen för att få sitt yrkeskompetensbevis förnyat. Vi erbjuder följande utbildningar:', 'route' => '#', 'btnText' => ['YKB GRUNDKURS 140 H' => '/kurser/ykb_140_h', 'YKB FORTBILDNING 35 H' => '/kurser/ykb_35_h']])
                    </div>
                    <!-- @icon('snowflake-2') @icon('snowflake-3') -->
                </div>
            </div>
        @endif

        {{--<div class="pt-3">--}}
        {{--<div class="container text-xs-center {{ $xmasCampaign? '' : 'mb-3' }}">--}}
        {{--<div class="row">--}}
        {{--<div class="col-md-12">--}}
        {{--<reco-reviews :custom-url="true"--}}
        {{--url="https://www.reco.se/korkortsjakten-ab?q=körkortsjakten"></reco-reviews>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--</div>--}}


        <div class="container text-xs-center mt-2">
            <div class="row">
                <div class="col-sm-12">
                    <h2>Tillsammans främjar vi körkortstagandet och bilismen i Sverige.</h2>
                </div>
                @include('elements.landing-block-bottom-img', ['src' => '/images/kak_logo_sigill.png', 'class' => 'logo-size-png', 'h3' => 'KAK - Kungliga Automobil Klubben', 'p' => 'grundat år 1903, är en rikstäckande allmännyttig ideell förening med främsta syfte att främja en sund utveckling av den svenska bilismen.', 'aHref' => 'https://kak.se/', 'aText' => 'Till KAKs hemsida'])
                @include('elements.landing-block-bottom-img', ['src' => '/images/kkj-logo-new.png', 'class' => 'logo-size-png','h3' => 'Körkortsjakten.se', 'p' => 'är Sveriges största mötesplats för trafikskolor och elever. Genom att erbjuda en one-stop-shop och en prisjämförelsetjänst underlättar vi körkortstagandet.', 'aHref' => route('shared::schools.index'), 'aText' => 'Till prisjämförelsen'])
                @include('elements.landing-block-bottom-img', ['src' => '/build/img/ic_logo.png', 'class' => 'logo-size-png','h3' => 'iKörkort', 'p' => 'iKörkort i samarbete med Körkortsjakten tillhandahåller Körkortsteori & Prov', 'aHref' => '/trafikskolor/stockholm/ikorkortnu-1585', 'aText' => 'Boka nu'])
            </div>
        </div>

    </div>

    <noscript>
        <ul>
            @foreach ($cities as $city) @if($city['school_count'] > 0)
                <li>
                    <a href="{{ route('shared::search.schools', ['citySlug' => $city['slug']]) }}">{{$city['name']}}</a>
                </li>
            @endif @endforeach
        </ul>
    </noscript>
@endsection

@section('scripts')
    <script>
        let maxBlurbHeight = 0,
            blurbImages = $('div.blurb');

        if (blurbImages.length) {
            $.each(blurbImages, (i, blurb) => {
                let h = $(blurb).innerHeight();
                maxBlurbHeight = maxBlurbHeight < h ? h : maxBlurbHeight;
            });

            blurbImages.attr('style', `height : ${maxBlurbHeight}px !important;`);
            blurbImages.find('p:first').css({
                position: 'absolute',
                bottom: 0
            });
        }
    </script>
@endsection
