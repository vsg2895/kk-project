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
                                <h1>Risk1 English / Riskettan</h1>
                            </div>
                        </div>

                        <!-- Header2 -->
                        <div class="row margin-top10px">
                            <div class="col-lg-12">
                                <h2>We list Risk Training Part 1 B</h2></div>
                        </div>

                        <!-- Description -->
                        <div class="row">
                            <div class="col-lg-12">
                                <h6>Find a time that suits you, book & pay online! Risk1 is the first part of the mandatory risk training for B driving licenses (part 2 is when you do a practical test in called car skid training test) </h6>
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
                                    <a href="{{ route('shared::engelskriskettan') }}" class="btn btn-primary button-find hitta">Hitta</a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="search-links-block">
                                    @include('shared::components.course_type.links', ['slug' => 'engelskriskettan'])
                                </div>
                            </div>
                        </div>

                        <div class="row margin-top15px">
                            <div class="col-lg-12">
                                <p>
                                    <strong>The mandatory risk training for B-driving licenses comprises two parts.</strong>
                                </p>
                                <p>
                                    Part 1 deals with alcohol, other drugs, fatigue and risky behavior in general.
                                </p>
                                <p>
                                    Part 2 corresponds to the old risk training (car skid training test) and deals with speed, safety and driving under special conditions.
                                </p>
                                <p>
                                    When you do your knowledge test and driving test, you must have done both parts of risk training. Risk training for motorcycles does not apply to passenger cars.
                                </p>
                            </div>
                            <div class="col-lg-12">
                                <p>
                                    Risk education part 1 is often called the "Risk1” or “Riskettan".
                                    The goal of the "Risk1” is to become a safe and responsible driver who is aware of the risks associated with traffic and vehicles.
                                    The training increases your awareness of dangerous behaviors and risks in traffic, how you can safely anticipate and manage these,
                                    and create the conditions for responsible driving. The training lasts just over three hours and is valid for five years.
                                </p>
                                <p>
                                    Meningen med kursen är att du ska bli en säkrare och bättre förare.
                                </p>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-6 search-page-img-parent">
                        <img src="/images/sub/riskettan.jpeg" class="search-page-img">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
