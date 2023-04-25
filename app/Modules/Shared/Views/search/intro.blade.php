@extends('shared::layouts.default')
@section('pageTitle', 'Introduktionskurs | Boka handledarkursen idag |')
@section('content')
    <div class="search-page">
        <div class="search-filters">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6">
                        <!-- Header1 -->
                        <div class="row">
                            <div class="col-lg-12 header1">
                                <h1>Introduktionskurser</h1>
                            </div>
                        </div>

                        <!-- Header2 -->
                        <div class="row margin-top10px">
                            <div class="col-lg-12">
                                <h2>Handledarutbildning för körkort - Boka och betala med Klarna Online</h2>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row">
                            <div class="col-lg-12">
                                <h6><b>Introduktionsutbildning (tidigare kallad handledarutbildning) är ett krav för att få övningsköra privat. Både du och den du ska köra med måste gå kursen.</b></h6>
                                <h6><b><i>Dessa kurser är fysiska i klassrum. Transportstyrelsen tillåter inte längre att dessa kurser genomför på distans/online.</i></b></h6>
                            </div>
                        </div>

                        <div class="row">
                            <!-- City selector -->
                            <div class="static-page-search-container" > 
                                <div class="search-input-container">
                                    <div id="landing-search-city" class="col">
                                        <semantic-dropdown :search="true" id="cities" placeholder="Sök stad" form-name="city_id" :data="{{ $cities }}">
                                            <template slot="dropdown-item" slot-scope="props">
                                                <div class="item" :data-value="props.item.slug">
                                                    <div class="item-text">@{{ props.item.name }}</div>
                                                </div>
                                            </template>
                                        </semantic-dropdown>
                                    </div>
                                </div>
                                <div class="search-btn-container">
                                    <a href="{{ route('shared::introduktionskurs') }}" class="btn btn-primary button-find hitta">Hitta</a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="search-links-block">
                                    <a href="/introduktionskurser/all" class="search-links">Allt</a>
                                    @include('shared::components.course_type.links', ['slug' => 'introduktionskurser'])
                                </div>
                            </div>
                        </div>

                        <div class="row margin-top15px">
                            <div class="col-lg-12">
                                <h3>Introduktionsutbildning/Handledarkurs</h3>
                                <p>

                                    <ul>
                                        <li>Körkortsutbildningens mål och innehåll samt regler för övningskörning</li>
                                        <li>Hur man planerar och strukturerar övningskörningen</li>
                                        <li>Viktiga faktorer för trafiksäkerheten och miljön</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

                        <p>
                            En introduktionsutbildning är minst tre timmar. Under utbildningen får du bland
                            annat information om vad du som handledare har för ansvar vid
                            övningskörningen, vilka krav och bedömningskriterier som gäller vid förarprov
                            (kunskapsprov och körprov). Du får också information om körsättets betydelse
                            för minskad förbrukning av drivmedel, lägre bränslekostnader och minskade
                            utsläpp av koldioxid samt hur viktig erfarenheten är för trafiksäkerheten. Man
                            diskuterar också på vilka sätt handledare och elev kan tolka samma
                            trafiksituation olika.
                        </p>

                    </div>
                    <div class="col-lg-6 search-page-img-parent">
                        <img class="search-page-img" src="/images/sub/introduktionskurser_new1.jpg">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
