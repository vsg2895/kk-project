@extends('shared::layouts.default') @section('pageTitle', 'Körkortsjakten - Sveriges trafikskolor |') @section('content')
    <div id="landing" class="container">
        <div class="license-front"></div>
        <div class="road road-right">
            <img src="/build/img/road-right.png">
        </div>

        <div id="landing-hero" class="d-flex align-items-center justify-content-center">
            <h1>Sveriges största mötesplats för elever och trafikskolor, jag vill lära mig köra:</h1>
        </div>

        <div class="landing-search-container">
            <form class="card" method="POST" action="{{ route('shared::index.search') }}">
                {{ csrf_field() }}

                <div class="landing-search-vehicle col-xs-12 col-lg-5 p-0">
                    <label class="h4 hidden-md-down">Jag vill lära mig köra</label>
                    <div class="search-filter-vehicle">
                        @foreach($vehicles as $indexKey => $vehicle)
                            <div class="search-filter-vehicle-wrapper col-xs-4"
                                 @if($indexKey==0) style="background-color: rgb(255, 232, 228); border-radius: 5px;" @endif >

                                <div class="form-checkbutton">
                                    <input id="{{ $vehicle->label }}"
                                           {{ $indexKey==0 ? 'checked' : '' }} value="{{ $vehicle->id }}" type="radio"
                                           name="vehicle_id"
                                    />
                                    <label for="{{ $vehicle->label }}">
                                        <img src="/build/img/{{ $vehicle->label }}.svg">
                                        <span class="vehicle-label">
                                            @switch($vehicle->identifier)
                                                @case('B')
                                                    <span>B</span>
                                                    <span>Personbil</span>
                                                @break
                                                @case('A')
                                                    <span>A Tung</span>
                                                    <span>motorcykel</span>
                                                @break
                                                @case('AM')
                                                    <span>AM</span>
                                                    <span>Moped klass I</span>
                                                @break
                                                @case('YKB')
                                                    <span>YKB</span>
                                                    <span>Förarutbildning</span>
                                                @break
                                            @endswitch
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="landing-search-dropdown col-xs-12 col-md-9 col-lg-5">
                    <label class="h4 hidden-md-down">i närheten av ...</label>
                    <semantic-dropdown :search="true" id="cities" placeholder="Välj stad" form-name="city_id" :data="{{ $cities }}">
                        <template slot="dropdown-item" slot-scope="props">
                            <div class="item" :data-value="props.item.id">
                                <div class="item-text">@{{ props.item.name }}</div>
                            </div>
                        </template>
                    </semantic-dropdown>
                </div>

                <div class="btn-search col-xs-12 col-md-3 col-lg-2 p-0">
                    <button type="submit" class="btn btn-black">Hitta trafikskola</button>
                </div>
            </form>
        </div>

{{--        todo remove .temp-div styles from app.scss after removing this code--}}
<!--        <div class="temp-div hidden-md-down">
            <div class="col-12 col-md-3 d-flex justify-content-center align-items-center">
                <img src="{{asset('images/landing/musikhjalpen.png')}}" alt="musikhjalpen">
            </div>
            <div class="col-12 col-md-6 text-center d-flex flex-column justify-content-center">
                <h2><b>Stöd Musikhjälpen tillsammans med Körkortsjakten
                        - För en tryggare barndom på flykt från krig.</b>
                </h2>
                <h2><b>Var även med och tävla om presentkort värde 10 000:-</b></h2>
                <a href="/musikhjalpen" class="btn btn-success">Mer Info</a>
            </div>
            <div class="col-12 col-md-3 d-flex justify-content-center">
                <img src="{{asset('images/landing/jultavling.png')}}" alt="jultavling">
            </div>
        </div>-->

<!--        <div class="temp-div hidden-md-up" style="margin-top: 220px;">
            <div class="col-12 col-md-6 text-center d-flex flex-column justify-content-center">
                <h2><b>Stöd Musikhjälpen tillsammans med Körkortsjakten
                        - För en tryggare barndom på flykt från krig.</b>
                </h2>
                <h2><b>Var även med och tävla om presentkort värde 10 000:-</b></h2>
                <a href="/musikhjalpen" class="btn btn-success ga-med-idag">Mer Info</a>
            </div>
            <div class="col-12 col-md-3 d-flex justify-content-center mt-2">
                <img src="{{asset('images/landing/jultavling.png')}}" alt="jultavling">
            </div>
        </div>-->

        <div class="road road-left">
            <img src="/build/img/road-left.png">
        </div>

        <div class="container text-xs-center">
            <div class="landing-block-row row">
                @include('elements.landing-block', ['class' => '', 'title' => 'INTRODUKTIONSKURS','image' => '/images/fl_1_intro.jpg', 'content' => 'Kurs för handledare och du som ska övningsköra . Hitta första, bästa, kurstillfälle i din stad. Boka och betala - snabbt, smidigt och säkert med Klarna.', 'route' => '/introduktionskurser', 'btnText' => 'Till kurserna', 'imageClass' => ''])
                @include('elements.landing-block', ['class' => '', 'title' => 'RISKUTBILDNING DEL <span class="upper-number">1</span>','image' => '/images/fl_2.png', 'content' => 'Även kallad Riskettan. Hitta första, bästa,  kurstillfälle i din stad.  Boka och betala - <b>snabbt, smidigt och säkert</b> med Klarna.', 'route' => route('shared::riskettan'), 'btnText' => 'Till kurserna', 'imageClass' => ''])
                @include('elements.landing-block', ['class' => '', 'title' => 'RISKTVÅAN (HALKAN)','image' => '/images/car.jpg', 'content' => 'I slutet av utbildningen för B-körkort är det dags att göra del två av den obligatoriska riskutbildningen (tidigare kallad halkutbildning). Den måste vara genomförd och giltig innan teori- och körprovet genomförs.', 'route' => route('shared::risktvaan'), 'btnText' => 'Till kurserna', 'imageClass' => ''])
            </div>
            <div class="landing-block-row-2 row">
                @include('elements.landing-block-bottom-img', ['src' => '/images/presentkort-hand.png', 'class' => 'logo-size-png','h3' => 'DIGITALT PRESENTKORT', 'p' => 'Nyckeln till en värld av frihet. Den perfekta presenten blev precis ännu bättre som passar vid alla tillfällen!', 'aHref' => route('shared::gift_card.index'), 'aText' => 'Presentkort'])
                @include('elements.landing-block', ['class' => '', 'title' => 'YKB Utbildningar','image' => '/images/ykb_35_h.jpeg', 'content' => 'YKB utbildning är ett lagkrav för chaufförer som kör lastbil alt. buss i yrkestrafik. Vart femte år behöver YKB beviset uppdateras med fortbildning för transportslagen för att få sitt yrkeskompetensbevis förnyat. Vi erbjuder följande utbildningar:', 'route' => '#', 'btnText' => ['YKB GRUNDKURS 140 H' => '/kurser/ykb_140_h', 'YKB FORTBILDNING 35 H' => '/kurser/ykb_35_h'], 'imageClass' => ''])
            </div>
        </div>

        <div class="container text-xs-center mt-3">
            <h2 class="hidden-lg-down">Tillsammans främjar vi körkortstagandet och bilismen i Sverige.</h2>
            <div class="landing-block-row-2 row">
                @include('elements.landing-block', ['class' => '', 'title' => 'KÖRKORTSTEORI OCH TESTPROV','image' => '/images/fl_1_prov_new.jpg', 'content' => 'Här får du tillgång till alla körkortsfrågor du behöver kunna för att klara teoriprovet. Alla körkortsfrågor har en förklaring som snabbt hjälper dig att förstå även det svåraste frågorna. Studera och testa dig när du vill, obegränsat antal prov.', 'route' => '/teoriprov-online', 'btnText' => 'Till kurserna', 'imageClass' => ''])
                @include('elements.landing-block-bottom-img', ['src' => '/images/teori-landing.png', 'class' => 'logo-size-png','h3' => 'iKörkort', 'p' => 'iKörkort i samarbete med Körkortsjakten tillhandahåller Körkortsteori & Prov', 'aHref' => '/trafikskolor/stockholm/ikorkortnu-1585', 'aText' => 'Boka nu'])
            </div>
        </div>

        <div class="hidden-lg-up">
            <div class="tillsammans">
                    <h2 class="tillsammans-header">Tillsammans främjar vi körkortstagandet och bilismen i Sverige.</h2>
                    <div class="d-flex justify-content-center">
                        <img class="imgcont" src="/images/kkj-logo-new.png" alt="recruit" />
                    </div>
                    <p>
                        <br/>
                        Körkortsjakten.se är Sveriges största mötesplats för trafikskolor och elever. Genom att erbjuda en one-stop-shop och en prisjämförelsetjänst underlättar vi körkortstagandet.
                    </p>
            </div>
        </div>

        <h3 class="prisjamforelse mb-2 hidden-md-up">Prisjämförelse:</h3>

        <div class="landing-search-container hidden-md-up">
            <form class="card" method="POST" action="{{ route('shared::index.search') }}">
                {{ csrf_field() }}

                <div class="landing-search-vehicle col-xs-12 col-md-5 p-0">
                    <label class="h4 hidden-md-down">Jag vill lära mig köra</label>
                    <div class="search-filter-vehicle">
                        @foreach($vehicles as $indexKey => $vehicle)

                            <div class="search-filter-vehicle-wrapper"
                                 @if($indexKey==0) style="background-color: rgb(255, 232, 228); border-radius: 5px;" @endif >
                                <div class="form-checkbutton">
                                    <input id="{{ $vehicle->label }}-2"
                                           {{ $indexKey==0 ? 'checked' : '' }} value="{{ $vehicle->id }}" type="radio"
                                           name="vehicle_id"
                                    />
                                    <label for="{{ $vehicle->label }}-2">
                                        <img src="/build/img/{{ $vehicle->label }}.svg">
                                        <span class="vehicle-label">
                                            @switch($vehicle->identifier)
                                                @case('B')
                                                    <span>B</span>
                                                    <span>Personbil</span>
                                                @break
                                                @case('A')
                                                    <span>A Tung</span>
                                                    <span>motorcykel</span>
                                                @break
                                                @case('AM')
                                                    <span>AM</span>
                                                    <span>Moped klass I</span>
                                                @break
                                                @case('YKB')
                                                    <span>YKB</span>
                                                    <span>Förarutbildning</span>
                                                @break
                                            @endswitch
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-xs-12 p-0">
                    <label class="h4 hidden-md-down">i närheten av ...</label>
                    <semantic-dropdown :search="true" id="cities-2" placeholder="Välj stad" form-name="city_id"
                                       :data="{{ $cities }}">
                        <template slot="dropdown-item" slot-scope="props">
                            <div  class="item" :data-value="props.item.id">
                                <div class="item-text">@{{ props.item.name }}</div>
                            </div>
                        </template>
                    </semantic-dropdown>
                </div>

                <div class="btn-search col-xs-12">
                    <button type="submit" class="btn btn-black">Jämför trafikskolor</button>
                </div>
            </form>
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
