@extends('student::layouts.default')
@section('content')
    <header class="section-header student-section">
        <h1 class="page-title">Mina omdömen</h1>
    </header>
     
    @include('shared::components.message')

    <div class="card card-block mx-0">
        @if($ratings->count())
            <div class="table">
                @foreach($ratings as $rating)
                    <div class="table-row">
                        <div class="table-cell col-sm-5 col-md-5">
                            <a href="{{ route('shared::schools.show', ['citySlug' => $rating->school->city->slug, 'slug' => $rating->school->slug]) }}">{{ $rating->school->name }}</a>
                        </div>
                        <div class="table-cell col-sm-7 col-md-3 text-sm-right text-md-center">
                            <span class="text-muted">Betyg:</span> <span class="school-rating">{{ $rating->rating }}</span>
                        </div>
                        <div class="table-cell col-sm-12 col-md-4 text-md-right">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('student::ratings.show', ['id' => $rating->id]) }}">Uppdatera</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <no-results title="Du har inte gett några omdömen än"></no-results>
        @endif
    </div>

    {!! $ratings->render('pagination::bootstrap-4') !!}
@endsection
