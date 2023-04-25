@extends('shared::layouts.default')
@section('pageTitle', 'Risktvåan MC - Boka riskutbildning del 1 för MC idag |')
@section('content')
    <div class="search-page">
        <div class="search-filters">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6">
                        <!-- Header1 -->
                        <div class="row">
                            <div class="col-lg-12 header1">
                                <h1>Risktvåan MC</h1>
                            </div>
                        </div>

                        <!-- Header2 -->
                        <div class="row margin-top10px">
                            <div class="col-lg-12">
                                <h2>Hitta och boka risktvåan MC (riskutbildning del2) hos Körkortsjakten.</h2>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row">
                            <div class="col-lg-12">
                                <h6>Hitta tid, boka & betala online! Risktvåan MC är del 2 i den obligatoriska riskutbildningen för MC-körkort (Del 1 handlar om alkohol, andra droger, trötthet och riskfyllda beteenden i övrigt.).</h6>
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
                                    <a href="{{ route('shared::risktvaanmc') }}" class="btn btn-primary button-find hitta">Hitta</a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="search-links-block">
                                    <a href="/risktvaanmc/all" class="search-links">Allt</a>
                                    @include('shared::components.course_type.links', ['slug' => 'risktvaanmc'])
                                </div>
                            </div>
                        </div>

                        <div class="row margin-top15px">
                            <div class="col-lg-12">
                                <p align="justify">
                                    <strong>Den obligatoriska riskutbildningen är speciellt inriktad på motorcykel</strong>
                                </p>
                                <p align="justify">
                                    Risktvåan MC är en praktisk kurs som handlar om de risker som kan uppstå i samband med motorcykelkörning.
                                </p>

                            </div>
                            <div class="col-lg-12">
                                <p align="justify">
                                    Innan du gör ditt kunskapsprov och körprov för behörighet A1, A2 och A måste du ha gjort
                                    båda delarna i riskutbildningen.
                                </p>
                                <p align="justify">
                                    Du som har ett giltigt körkort för motorcykel och har gått riskutbildning för motorcykel,
                                    behöver inte gå någon ny riskutbildning när du ska göra körprov för en högre
                                    motorcykelbehörighet.
                                </p>
                                <p align="justify">
                                    Riskutbildning för personbil gäller inte för motorcykel.
                                </p>
                                <p align="justify">
                                    Meningen med kursen är att du ska bli en säkrare och bättre förare.
                                </p>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-6">
                        <img src="/images/sub/risktvaanmc.jpeg">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
