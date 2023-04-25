@extends('organization::layouts.default')
@section('content')
    <header class="section-header">
        <nav class="d-flex mb-1">
            <a class="btn btn-sm btn-primary" href="{{ route('organization::schools.create') }}">Lägg upp ny trafikskola</a>
        </nav>
        <h1 class="page-title">Trafikskolor och Priser</h1>
    </header>

     @include('shared::components.message')

    <div class="card card-block mx-0">
        @if($schools->count())
            <div class="table">
                <div class="table-head table-row hidden-sm-down">
                    <div class="table-cell col-md-3">
                        Namn
                    </div>
                    <div class="table-cell col-md-2">
                        Betyg
                    </div>
                    <div class="table-cell col-md-3">
                        Stad
                    </div>
                    <div class="table-cell col-md-4">
                    </div>
                </div>
                @foreach($schools as $school)
                    <div class="table-row">
                        <div class="table-cell hidden-md-up more-button">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('organization::schools.show', ['id' => $school->id]) }}">Visa</a>
                        </div>
                        <div class="table-cell col-md-3">
                            <a href="{{ route('organization::schools.show', ['id' => $school->id]) }}">{{ $school->name }}</a>
                        </div>
                        <div class="table-cell col-md-2">
                            <span class="school-rating h3 mb-0">{{ $school->average_rating }}</span>
                        </div>
                        <div class="table-cell col-md-3">
                            {{ $school->city->name }}
                        </div>
                        <div class="table-cell col-md-4 d-flex justify-content-end">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('organization::schools.show', ['id' => $school->id]) . '#prices' }}">Redigera priser</a>
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('organization::schools.show', ['id' => $school->id]) . '#courses' }}">Redigera kurser</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <no-results title="Du har inga trafikskolor än"></no-results>
        @endif
    </div>

    {!! $schools->render('pagination::bootstrap-4') !!}
@endsection
