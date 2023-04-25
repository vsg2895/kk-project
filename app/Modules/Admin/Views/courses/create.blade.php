@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <nav class="d-flex">
            <a class="back-button btn btn-sm btn-outline-primary" href="{{ route('admin::courses.index') }}">@icon('arrow-left') Tillbaka</a>
            @if($initialSchool)
            <a class="back-button btn btn-sm btn-outline-primary" href="{{ route('admin::schools.show', ["id" => $initialSchool]) }}#courses">@icon('arrow-left') Tillbaka till trafikskolan</a>
            @endif
        </nav>
        <h1 class="page-title">Ny kurs</h1>
    </header>

    @include('shared::components.errors')

    <div class="card card-block mx-0">
        <form method="POST" action="{{ route('admin::courses.store') }}">
            {{ csrf_field() }}
            <course-form
                :old-data="{{ json_encode(old()) }}"
                :is-admin="true"
                :initial-course="{{ json_encode($initialCourse) }}"
                @if($initialSchool) :initial-school="{{ $initialSchool }}" @endif
            ></course-form>
            <button class="btn btn-success">Spara</button>
        </form>
    </div>
@endsection
@section('scripts')
    @parent
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_API_KEY') }}&libraries=places&language=sv&region=se"></script>
@endsection
