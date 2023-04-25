@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <h1 class="page-title">Städer</h1>
    </header>

    @include('shared::components.message')
    @include('shared::components.info')

    @component('form.search')@endcomponent

    <div class="card card-block mx-0">
        @if($cities->count())
            <div class="table">
                <div class="table-head table-row hidden-sm-down">
                    <div class="table-cell col-md-6">
                        Namn
                    </div>
                    <div class="table-cell col-md-6">
                        Handling
                    </div>
                </div>
                @foreach($cities as $city)
                    <div class="table-row">
                        <div class="table-cell col-md-6">
                            <a href="{{ route('shared::search.schools', ['citySlug' => $city->slug]) }}" target="_blank">
                                {{ $city->name }}
                            </a>
                        </div>

                        <div class="col-md-6 d-flex">
                            <a href="{{ route('admin::cities.show', $city->id) }}" class="btn btn-sm btn-outline-primary">
                                Redigera beskrivning
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <no-results title="Inga organisationer hittades"></no-results>
        @endif
    </div>

    {!! $cities->render('pagination::bootstrap-4') !!}
@endsection
