@extends('shared::layouts.default')
@section('pageTitle', 'Riskettan | Boka riskutbildning idag |')
@section('content')
    <div class="search-page">
        <div class="search-filters">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6">
                        <!-- Header1 -->
                        <div class="row">
                            <div class="col-lg-12 header1">
                                <h1>Riskettan</h1>
                            </div>
                        </div>

                        <!-- Header2 -->
                        <div class="row margin-top10px">
                            <div class="col-lg-12">
                                <h3>Vi listar Riskutbildning del 1 B-körkort</h3>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row">
                            <div class="col-lg-12">
                                <h6>Hitta tid, boka & betala online! Riskettan är del 1 i den obligatoriska riskutbildningen för B-körkort (del 2 är halkbanan).</h6>
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
                                    <a href="{{ route('shared::riskettan') }}" class="btn btn-primary hitta">Hitta</a>
                                </div>
                           </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="search-links-block">
                                    <a href="/riskettan/all" class="search-links">Allt</a>
                                    @include('shared::components.course_type.links', ['slug' => 'riskettan'])
                                </div>
                            </div>
                        </div>

                        <div class="row margin-top15px">
                            <div class="col-lg-12">
                                <p>Den obligatoriska riskutbildningen för B-körkort omfattar två delar.</p>
                                <ul>
                                    <li><b>Del 1</b> handlar om alkohol, andra droger, trötthet och riskfyllda beteenden i övrigt.</li>
                                    <li><b>Del 2</b> motsvarar den gamla riskutbildningen (halkbanan) och handlar om hastighet, säkerhet och körning under särskilda förhållanden.</li>
                                </ul>
                                <p>
                                    När du gör ditt kunskapsprov och körprov måste du ha gjort riskutbildningens båda delar.
                                    Riskutbildning för motorcykel gäller inte för personbil.
                                </p>
                            </div>
                            <div class="col-lg-12">
                                <p>
                                    Riskutbildning del 1 kallas ofta för “Riskettan”.
                                    Målet med ”Riskettan bil” är att du skall bli en säker och ansvarsfull förare som är medveten om risker med trafik och fordon.
                                    Utbildningen ökar din medvetenhet om farliga beteenden och risker i trafiken, hur du på ett säkert sätt kan förutse och hantera dessa samt skapa förutsättningar för ett ansvarsfullt körande.
                                    Utbildningen varar drygt tre timmar och är giltig i fem år.
                                </p>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-6 search-page-img-parent">
                        <img src="/images/sub/riskettan-new.jpg" class="search-page-img">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
