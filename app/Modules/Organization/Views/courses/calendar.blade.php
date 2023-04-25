@extends('organization::layouts.default')
@section('content')

{{--    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>--}}

    @if(!$schoolsCount)
        <div class="alert alert-danger" role="alert">
            <strong>Du måste skapa en trafikskola innan du kan lägga upp en kurs.</strong>
        </div>
    @else
        <div class="section-header section-header-calendar d-flex justify-content-between">
            <div>
                <nav class="d-flex mb-1">
                    <a class="btn btn-sm btn-primary" href="{{ route('organization::courses.index') }}">Kurslista</a>
                    <a class="btn btn-sm btn-primary" href="{{ route('organization::courses.create') }}">Lägg upp ny kurs</a>
                </nav>
                <h1 class="page-title">Kurser</h1>
            </div>
            <a class="btn btn-sm btn-primary" href="{{ route('shared::page.top-partner') }}">
                Bli Top Partner &nbsp;<i class="fa fa-medal top-partner-medal-header"></i>
            </a>
        </div>

        @include('shared::components.message')

        <div class="card card-block mx-0">
            <loyalty-progress :schools="{{ $schools }}" :loyalty-levels="{{ $loyaltyLevels }}" />
        </div>

        <div class="card card-block mx-0">
            <h3>Kommande kurser</h3>

            <courses-scheduler :initial-courses="{{ $courses }}" :schools="{{ $schools }}" csrf-token="{{ csrf_token() }}"></courses-scheduler>
        </div>
    @endif
@endsection
