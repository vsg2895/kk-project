@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('admin::cities.index') !!}">@icon('arrow-left') Tillbaka</a>
        <h1 class="page-title">{{ $city->name }}</h1>
    </header>

    @include('shared::components.message')
    @include('shared::components.errors')

    <div class="card card-block">
        <form action="{{ route('admin::cities.update', ['id' => $city->id]) }}" method="post">
            {{ csrf_field() }}

{{--            <div class="form-group">--}}
{{--                <label>Best deal Trafikskola</label>--}}
{{--                <semantic-dropdown form-name="school_id" :search="true"--}}
{{--                                   :readonly="false" :initial-item="{{ $city->bestDeal ? $city->bestDeal : 'false' }}" placeholder="Välj trafikskola"--}}
{{--                                   :data="{{ $city->schools }}">--}}
{{--                    <template slot="custom-dropdown-items">--}}
{{--                        <div class="item" :data-value="0">--}}
{{--                            <div class="item-text">No best deal</div>--}}
{{--                        </div>--}}
{{--                    </template>--}}
{{--                    <template slot="dropdown-item" slot-scope="props">--}}
{{--                        <div class="item" :data-value="props.item.id">--}}
{{--                            <div class="item-text">@{{ props.item.name }}</div>--}}
{{--                        </div>--}}
{{--                    </template>--}}
{{--                </semantic-dropdown>--}}
{{--            </div>--}}

            <div class="form-group">
                <label>Best deal Trafikskola</label>
                <semantic-dropdown form-name="best_schools_id"  search="true" :initial-item="{{ $city->bestDeals ? '{"id":  "' . implode(',', $city->bestDeals->pluck('id')->all()) . '"}' : 'false' }}" multiple="true"
                                   placeholder="Välj trafikskolor" formName="school_id"
                                   :data="{{ $city->schools }}">
                    <template slot="dropdown-item" slot-scope="props">
                        <div class="item" :data-value="props.item.id">
                            <div class="item-text" v-text="props.item.name"></div>
                            <div class="item-description">i <span v-text="props.item['city_name']"></span></div>
                        </div>
                    </template>
                </semantic-dropdown>
            </div>

            <label for="school_description">Best deal Trafikskola description</label>
            <div class="form-group">
                <textarea name="school_description" id="school_description" class="form-control">{{ $city->bestDeal ? $city->school_description : '' }}</textarea>
            </div>

            <input type="hidden" name="id" value="{{ $city->id }}">
            <label for="desc_trafikskolor">Trafikskolor</label>
            <div class="form-group">
                <textarea name="desc_trafikskolor" id="desc_trafikskolor" class="form-control">{{ ($city->info) ? $city->info->desc_trafikskolor : '' }}</textarea>
            </div>
            <label for="desc_introduktionskurser">Introduktionskurser</label>
            <div class="form-group">
                <textarea name="desc_introduktionskurser" id="desc_introduktionskurser" class="form-control">{{ ($city->info) ? $city->info->desc_introduktionskurser : '' }}</textarea>
            </div>
            <label for="desc_riskettan">Riskettan</label>
            <div class="form-group">
                <textarea name="desc_riskettan" id="desc_riskettan" class="form-control">{{ ($city->info) ? $city->info->desc_riskettan : '' }}</textarea>
            </div>
            <label for="desc_teorilektion">Teorilektion</label>
            <div class="form-group">
                <textarea name="desc_teorilektion" id="desc_teorilektion" class="form-control">{{ ($city->info) ? $city->info->desc_teorilektion : '' }}</textarea>
            </div>
            <label for="desc_risktvaan">Risktvåan</label>
            <div class="form-group">
                <textarea name="desc_risktvaan" id="desc_risktvaan" class="form-control">{{ ($city->info) ? $city->info->desc_risktvaan : '' }}</textarea>
            </div>
            <label for="desc_riskettanmc">Riskettan MC</label>
            <div class="form-group">
                <textarea name="desc_riskettanmc" id="desc_riskettanmc" class="form-control">{{ ($city->info) ? $city->info->desc_riskettanmc : '' }}</textarea>
            </div>
            <label for="desc_riskettanmc">Search Radius</label>
            <div class="form-group">
                <input type="number" name="search_radius" id="search_radius" class="form-control" value="{{ $city->search_radius ? (int)$city->search_radius : 0 }}">
            </div>

            <div class="input-group">
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit">Uppdatera</button>
                </span>
            </div>
        </form>
    </div>
@endsection
