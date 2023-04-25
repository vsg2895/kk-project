@extends('organization::layouts.default')
@section('content')
    <header class="section-header">
        <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('organization::schools.index') !!}">@icon('arrow-left') Tillbaka</a>
        <h1 class="page-title">Ny trafikskola</h1>
    </header>

    @include('shared::components.errors')

    <div class="card card-block">
        <form method="POST" action="{{ route('organization::schools.store') }}">
            @include('shared::components.school.create')
            <button class="btn btn-success" type="submit">Skapa</button>
        </form>
    </div>
@endsection
@section('scripts')
    @parent
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_API_KEY') }}&libraries=places&language=sv&region=se"></script>
@endsection

