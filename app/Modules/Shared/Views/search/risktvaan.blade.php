@extends('shared::layouts.default')
@section('pageTitle', 'Risktvåan - Boka riskutbildning del 2 idag |')
@section('content')

    <div class="search-page">
        <div class="search-filters">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6">
                        <!-- Header1 -->
                        <div class="row">
                            <div class="col-lg-12 header1">
                                <h1>Risktvåan</h1>
                            </div>
                        </div>

                        <!-- Header2 -->
                        <div class="row margin-top10px">
                            <div class="col-lg-12">
                                <h2>Hitta och boka risktvåan (halkan) hos Körkortsjakten.</h2>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row">
                            <div class="col-lg-12">
                                <h6>I slutet av utbildningen för B-körkort är det dags att göra del två av den obligatoriska
                                    riskutbildningen (tidigare kallad halkutbildning).
                                    Den måste vara genomförd och giltig innan teori- och körprovet genomförs.
                                </h6></div>
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
                                    <a href="{{ route('shared::risktvaan') }}" class="btn btn-primary button-find hitta">Hitta</a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="search-links-block">
                                    <a href="/risktvaan/all" class="search-links">Allt</a>
                                    @include('shared::components.course_type.links', ['slug' => 'risktvaan'])
                                </div>
                            </div>
                        </div>

                        <div class="row margin-top15px">
                            <div class="col-lg-12">
                                <p>
                                    Målet med utbildningen är att du efter att den är genomförd ska ha upplevt risker
                                    med olika hastigheter och körning under särskilda förhållanden. Du ska få möjlighet
                                    till en värdering av hastighetens, väglagets, fordonets och din egen förmåga som
                                    förare att påverka en uppkommen situation. Detta uppnås dels genom praktiska
                                    moment, men även genom diskussion samt reflektion med lärare och övriga
                                    deltagare i gruppen. Syftet med utbildningen är att belysa problematiken kring
                                    hastighet, säkerhet och körning under särskilda förhållanden. För att bli godkänd ska
                                    du nå upp till målen som är angivna i Trafikverkets föreskrifter om Riskutbildning del
                                    2, bil.
                                </p>
                            </div>
                            <div class="col-lg-12">
                                <p>
                                    Ni behöver inte vara oroliga för halkkörningen. Du ska tänka på att lyssna på
                                    instruktionerna så kommer det gå bra. Det är viktigt att du lär känna bilens funktioner
                                    på halt väglag. Många tycker det är riktigt kul att göra halkbanan.
                                </p>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-6 search-page-img-parent">
                        <img class="search-page-img" src="/images/sub/risktvaan-new.jpg">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
