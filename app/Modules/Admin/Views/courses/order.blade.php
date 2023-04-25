@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
{{--        <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('admin::cities.index') !!}">@icon('arrow-left') Tillbaka</a>--}}
{{--        <h1 class="page-title">{{ $city->name }}</h1>--}}
    </header>

    @include('shared::components.message')
    @include('shared::components.errors')

    <div class="card card-block">
        <form action="{{ route('admin::courses.order') }}" method="post">
            {{ csrf_field() }}

            <date-picker-form selected-day="{{ $date }}"></date-picker-form>


            @if(count($vehicles))
                <label>Courses</label>
                <semantic-dropdown
                    placeholder="Course type" :on-item-selected="function (val) {
                    var url = window.location.href;
                    if (url.indexOf('?') > -1){
                        if (url.indexOf('type=' + val.id) > -1){
                            return;
                        } else if (url.indexOf('type=') > -1){
                        let regex = new RegExp('[?&]type(=([^&#]*)|&|#|$)'),
                            results = regex.exec(url);
                            url = url.replace(results[1], '=' + val.id)
                        } else {
                            url += '&type=' + val.id
                        }

                    } else {
                       url += '?type=' + val.id
                    }
                    window.location.href = url; }" placeholder="Select city" form-name="type" search="true" formName="type" :initial-item="{{ $selectedType }}" :data="{{ json_encode($vehicles) }}">
                    <template slot="dropdown-item" slot-scope="props">
                        <div class="item" :data-value="props.item.id">
                            <div class="item-text">@{{ props.item.label }} (@{{ props.item.vehicle.label}})</div>
                        </div>
                    </template>
                </semantic-dropdown>
            @endif

            @if(count($cities))
                <label>Cities</label>
                <semantic-dropdown
                    placeholder="City" :on-item-selected="function (val) {
                    var url = window.location.href;
                    if (url.indexOf('?') > -1){
                        if (url.indexOf('city=' + val.id) > -1){
                            return;
                        } else if (url.indexOf('city=') > -1){
                            let regex = new RegExp('[?&]city(=([^&#]*)|&|#|$)'),
                            results = regex.exec(url);
                            url = url.replace(results[1], '=' + val.id)
                        } else {
                            url += '&city=' + val.id
                        }

                    }else{
                       url += '?city=' + val.id
                    }
                    window.location.href = url; }" placeholder="Select city" form-name="city" search="true" formName="city" :initial-item="{{ $selectedCity }}" :data="{{ json_encode($cities) }}">
                    <template slot="dropdown-item" slot-scope="props">
                        <div class="item" :data-value="props.item.id">
                            <div class="item-text">@{{ props.item.name }}</div>
                        </div>
                    </template>
                </semantic-dropdown>
            @endif

            <div class="form-group">
                <label>All Trafikskola</label>
                <semantic-dropdown form-name="schools_id"  search="true"
                                   :initial-item="'false'"
                                   multiple="true"
                                   placeholder="Välj trafikskolor"
                                   formName="school_id"
                                   readonly="{{ !(int)$selectedCity || !(int)$selectedType }}"
                                   :data="{{ $schools }}">
                    <template slot="dropdown-item" slot-scope="props">
                        <div class="item" :data-value="props.item.id">
                            <div class="item-text" v-text="props.item.name"></div>
                            <div class="item-description">i <span v-text="props.item.city.name"></span></div>
                        </div>
                    </template>
                </semantic-dropdown>
            </div>

            <div class="input-group">
                <span class="input-group-btn">
                    <button @if(!(int)$selectedCity || !(int)$selectedType || !count($schools)) disabled="disabled" @endif class="btn btn-primary" type="submit">Uppdatera</button>
                </span>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
    <script lang="text/javascript">
        let editor = CodeMirror.fromTextArea(document.getElementById('content'), {
            lineNumbers: true,
            mode: "htmlmixed",
            indentWithTabs: true,
            viewportMargin: 25,

        });


    </script>
@endsection
