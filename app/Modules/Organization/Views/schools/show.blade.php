@extends('organization::layouts.default')
@section('content')
    <header class="section-header"> 
        <div class="d-flex">
            @if(auth()->user()->organization->schools->count() > 1)
                <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('organization::schools.index') !!}">@icon('arrow-left') Tillbaka</a>
            @endif

            <a class="btn btn-sm btn-outline-primary" href="{{ route('organization::schools.create') }}"> Lägg till en ny trafikskola</a>
        </div>

        <h1 class="page-title">{{ $school->name }} <a class="small text-muted" href="{{ route('shared::schools.show', ['citySlug' => $school->city->slug, 'schoolSlug' => $school->slug]) }}" target="_blank">Besök skolsida @icon('arrow-right')</a></h1>
        <h3>Betyg: <span class="school-rating">{{ $school->average_rating }}</span></h3>
    </header>

    @include('shared::components.message')
    @include('shared::components.errors')

    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#tab-info" role="tab">Skolans detaljer</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab-prices" role="tab">Redigera priser</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab-courses" role="tab">Kurser</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab-gallery" role="tab">Gallery</a>
        </li>
    </ul>
    <div class="card card-block">
        <div class="tab-content">
            @include('shared::components.school.edit')
            @if(auth()->user()->isAdmin())
                <div class="tab-pane" id="tab-comments" role="tabpanel">
                    @include('admin::components.annotation.list', ['list' => $school->comments])
                </div>
            @endif
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_API_KEY') }}&libraries=places&language=sv&region=se"></script>
@endsection
