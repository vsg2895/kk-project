@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <nav class="d-flex flex-md-row flex-wrap">
            <a class="btn btn-sm btn-primary mb-1" href="{{ route('admin::schools.create') }}">Lägg till trafikskola</a>
            <a class="btn btn-sm btn-primary mb-1" href="{{ route('admin::courses.order') }}">Ändra trafikskolornas prioritet</a>
        </nav>
        <h1 class="page-title">Trafikskolor och priser</h1>
    </header>

    @include('shared::components.message')
    @include('shared::components.info')

    @component('form.search')
        @slot('addons')
            <div class="form-group mt-1">
                <label>Filter kurser</label>
                <div class="d-flex">
                    @foreach([['value' => 'coming', 'label' => 'Har kommande'], ['value' => 'passed', 'label' => 'Har passerade'], ['value' => 'none', 'label' => 'Har inga'], ['value' => 'notMember', 'label' => 'Ej medlem'], ['value' => 'connected', 'label' => 'Connected']] as $status)
                        <div class="mr-1">
                            <input id="{{ $status['value'] }}" value="{{ $status['value'] }}" @if(Request::has('status') && in_array($status['value'], Request::get('status'))) checked @endif type="checkbox" name="status[]" />
                            <label for="{{ $status['value'] }}">{{ $status['label'] }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endslot
    @endcomponent

    <div class="card card-block mx-0">
        @if($schools->count())
            <div class="table">
                <div class="table-head table-row hidden-sm-down">
                    <div class="table-cell col-md-4">Trafikskola</div>
                    <div class="table-cell col-md-1">Connected</div>
                    <div class="table-cell col-md-2">Available Course</div>
                    <div class="table-cell col-md-1">Past Course</div>
                    <div class="table-cell col-md-1">Uppdaterad</div>
                    <div class="table-cell col-md-3"></div>
                </div>
                @foreach($schools as $school)
                    <div class="table-row">
                        <div class="table-cell col-md-4 d-flex justify-content-between">
                            <div class="name-content">
                                @if($school->deleted_at)
                                    <span class="text-danger">Borttagen</span>
                                @endif

                                <a href="{{ route('admin::schools.show', ['id' => $school->id]) }}">
                                    {{ $school->name }} - {{ $school->city->name }}
                                </a>
                                @if($school->claims->count())
                                    <span class="text-danger">Väntande anspråk att hantera</span>
                                @endif
                                <strong class="text-muted">(#{{ $school->id }})</strong>
                            </div>
                            <div class="button-content">
                                <a href="{{ route('shared::page.iframe', $school->id) }}"
                                    class="copy float-end d-flex align-items-center btn btn-sm btn-outline-primary">
                                    Iframe Link
                                </a>
                            </div>
                        </div>
                        <div class="table-cell col-md-1 text-center">
                            <input class="connected-check" id="connected-{{$school->id}}" value="{{ $school->connected_to }}"
                                   data-link="{{route('api::schools.update.connected', $school)}}"
                                   @if($school->connected_to) checked
                                   @endif type="checkbox"/>
                        </div>
                        <div class="table-cell col-md-2">
                            @if($school->upcomingCourses->count())
                                @include('shared::components.datetime', ['date' => $school->upcomingCourses()->orderBy('start_time', 'DESC')->first()->start_time, 'showOriginal' => true])
                            @else
                                No course
                            @endif
                        </div>
                        <div class="table-cell col-md-1">
                            @if($school->courses()->whereDate('start_time', '<', Carbon\Carbon::now('Europe/Stockholm')->format("Y-m-d H:i:s"))->count())
                                @include('shared::components.datetime', ['date' => $school->courses()->whereDate('start_time', '<', Carbon\Carbon::now('Europe/Stockholm')
                                                            ->format("Y-m-d H:i:s"))
                                                            ->orderBy('start_time', 'DESC')->first()->start_time, 'showOriginal' => true])
                            @else
                                No course
                            @endif
                        </div>
                        <div class="table-cell col-md-1">
                            @include('shared::components.datetime', ['date' => $school->updated_at, 'showOriginal' => true])
                        </div>
                        <div class="table-cell hidden-md-up d-flex justify-content-end mb-1 pr-0">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin::schools.show', ['id' => $school->id]) }}">Visa</a>
                        </div>
                        <div class="col-xs-12 col-md-3 d-flex justify-content-end px-0">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin::schools.show', ['id' => $school->id]) . '#prices' }}">Redigera priser</a>
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin::schools.show', ['id' => $school->id]) . '#courses' }}">Redigera kurser</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <no-results title="Inga trafikskolor hittades"></no-results>
        @endif
    </div>

    {!! $schools->render('pagination::bootstrap-4') !!}
@endsection

@section('no-vue')
    <script>
        $(document).ready(function() {
            $('.connected-check').click(function () {
                let _this = $(this);
                let checked = _this.is(":checked");
                fetch(_this.data('link'), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    body: JSON.stringify({connected_to: checked})
                })           //api for the get request
                    .then(response => response.json())
                    .then(data => console.log(data));
            });

            $(document).on('click', '.copy', function (e) {
                e.preventDefault();
                let copyText = $(this).attr('href');
                navigator.clipboard.writeText(copyText);
                $('.copy').text('Iframe Link');
                $('.copy').removeClass('btn-outline-success');
                $('.copy').addClass('btn-outline-primary');
                $(this).removeClass('btn-outline-primary');
                $(this).addClass('btn-outline-success')
                $(this).text('Copied');
            })
        })
    </script>
@endsection

