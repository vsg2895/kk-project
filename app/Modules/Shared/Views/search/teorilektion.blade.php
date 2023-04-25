@extends('shared::layouts.default')
@section('pageTitle', 'Korlektion bil | Boka korlektion idag |')
@section('content')
    <div class="search-page">
        <div class="search-filters">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6">
                        <!-- Header1 -->
                        <div class="row">
                            <div class="col-lg-12 header1">
                                <h1>Körlektion</h1>
                            </div>
                        </div>

                        <!-- Header2 -->
                        <div class="row margin-top10px">
                            <div class="col-lg-12">
                                <h2>Hitta en tid som passar dig och boka körlektion online</h2>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row">
                            <div class="col-lg-12">
                                <h6>Hitta tid, boka och betala online med Klarna. Att köra med en utbildad trafikskolelärare är det bästa sättet att lära sig köra bil.Här finner du priser för enstaka lektioner Välkommen!</h6>
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
                                    <a href="{{ route('shared::teorilektion') }}" class="btn btn-primary hitta">Hitta</a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="search-links-block">
                                    <a href="{{ route('shared::teorilektion') }}/all" class="search-links">Allt</a>
                                    @include('shared::components.course_type.links', ['slug' => 'korlektion'])
                                </div>
                            </div>
                        </div>

                        <div class="row margin-top15px">
                            <div class="col-lg-12">
                                <p>
                                    Förbered dig för uppkörningen med lärarledda körlektioner.
                                </p>
                                <p>
                                    Tips! Ibland är det mer förmånligt att förbetala för ett större antal körlektioner.
                                    Ofta rabatterar trafikskolorna priset när du betalar för fem eller fler lektioner i förväg.
                                </p>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-6">
                        <img src="/images/sub/korlektion.jpeg">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
