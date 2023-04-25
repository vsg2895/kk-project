@extends('shared::layouts.default')
@section('pageTitle', $vehicle->title . ' - '. $vehicle->description .'|')
@section('content')
    <div class="search-page">
        <div class="search-filters">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6">
                        <!-- Header1 -->
                        <div class="row">
                            <div class="col-lg-12 header1">
                                <h1>{{ $vehicle->title }}</h1>
                            </div>
                        </div>

                        <!-- Header2 -->
                        <div class="row margin-top10px">
                            <div class="col-lg-12">
                                @if ($vehicle->id !== \Jakten\Models\VehicleSegment::YKB_35_H && $vehicle->id !== \Jakten\Models\VehicleSegment::YKB_140_H)
                                    <h2>{!! $vehicle->description !!}</h2>
                                @else
                                    <h6>{!! $vehicle->description !!}</h6>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row">
                            <div class="col-lg-12">
                                <h6>{!! $vehicle->sub_description !!}</h6>
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
                                    <a href="{{ route('shared::search.courses', ['slug' => $vehicle->slug]) }}" class="btn btn-primary hitta">Hitta</a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="search-links-block">
                                    <a href="/kurser/{{ $vehicle->slug }}/all" class="search-links">Allt</a>
                                    @include('shared::components.course_type.links', ['slug' => 'kurser/' . $vehicle->slug])
                                </div>
                            </div>
                        </div>
                        <h3 class="margin-top15px">{{ $vehicle->explanation }}</h3>
                        <div class="row">
                            <div class="col-lg-12">
                                <p>
                                    {!! $vehicle->sub_explanation !!}
                                </p>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-6" style="max-width: 800px;">
                        @if($vehicle->slug == 'risk1+2')
                            <img src="/images/sub/risk12.jpg?time={{ $vehicle->updated_at }}1" style="width: 100%">
                        @else
                            <img src="/images/sub/{{ $vehicle->slug }}.jpeg?time={{ $vehicle->updated_at }}" style="width: 100%">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@if(isset($showModal) && $showModal)
    @include('shared::components.bonus-modal')
@endif
