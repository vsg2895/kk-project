@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <nav class="page-nav">
            <a class="btn btn-sm btn-primary" href="{{ route('admin::courses.create') }}">Lägg upp ny kurs</a>
        </nav>
        <h1 class="page-title">Kurser</h1>
    </header>

    @include('shared::components.message')

    <div class="card card-block">
        <h3>Kommande kurser</h3>
        @if($courses->count())
            <div class="table">
                <div class="table-head table-row hidden-sm-down">
                    <div class="table-cell col-md-3">
                        Datum/tid
                    </div>
                    <div class="table-cell col-md-2">
                        Namn
                    </div>
                    <div class="table-cell col-md-1 text-md-center">
                        Deltagare
                    </div>
                    <div class="table-cell col-md-2 text-md-center">
                        Tillgängliga platser
                    </div>
                    <div class="table-cell col-md-4 text-md-right">
                        Trafikskola
                    </div>
                </div>
                @foreach($courses as $course)
                    <div class="table-row">
                        <div class="table-cell hidden-md-up more-button">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin::courses.show', ['id' => $course->id]) }}">Visa</a>
                        </div>
                        <div class="table-cell col-md-3">
                            <a href="{{ route('admin::courses.show', ['id' => $course->id]) }}">{{ $course->start_time->formatLocalized('%A %d:e %B, %Y (%H:%M)') }}</a>
                        </div>
                        <div class="table-cell col-md-2">
                            {{ $course->name }} ({{ $course->segment->vehicle->label }})
                        </div>
                        <div class="table-cell col-md-1 text-md-center @if(!$course->bookings->where('cancelled', false)->count()) text-muted @else text-success @endif">
                            {{ $course->bookings->where('cancelled', false)->count() }}<span class="hidden-md-up"> deltagare</span>
                        </div>
                        <div class="table-cell col-md-2 text-md-center @if($course->seats > 0) text-success @else text-muted @endif">
                            {{ $course->seats }} <span class="hidden-md-up"> platser kvar</span>
                        </div>
                        <div class="table-cell col-md-4 text-md-right">
                            <a href="{{ route('admin::schools.show', ['id' => $course->school->id]) }}">{{ $course->school->name }} i {{ $course->school->city->name }}</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <no-results title="Inga kurser hittades"></no-results>
        @endif
    </div>

    {!! $courses->render('pagination::bootstrap-4') !!}

    <div class="card card-block">
        <h3>Avklarade kurser</h3>
        @if($oldCourses->count())
            <div class="table">
                <div class="table-head table-row hidden-sm-down">
                    <div class="table-cell col-md-4">
                        Datum/tid
                    </div>
                    <div class="table-cell col-md-3">
                        Namn
                    </div>
                    <div class="table-cell col-md-1 text-md-center">
                        Deltagare
                    </div>
                    <div class="table-cell col-md-4 text-md-right">
                        Trafikskola
                    </div>
                </div>
                @foreach($oldCourses as $course)
                    <div class="table-row">
                        <div class="table-cell hidden-md-up more-button">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin::courses.show', ['id' => $course->id]) }}">Visa</a>
                        </div>
                        <div class="table-cell col-md-4">
                            <a href="{{ route('admin::courses.show', ['id' => $course->id]) }}">{{ $course->start_time->formatLocalized('%A %d:e %B, %Y (%H:%M)') }}</a>
                        </div>
                        <div class="table-cell col-md-3">
                            {{ $course->name }} ({{ $course->segment->vehicle->label }})
                        </div>
                        <div class="table-cell col-md-1 text-md-center @if(!$course->bookings->where('cancelled', false)->count()) text-muted @else text-success @endif">
                            {{ $course->bookings->where('cancelled', false)->count() }}<span class="hidden-md-up"> deltagare</span>
                        </div>
                        <div class="table-cell col-md-4 text-md-right">
                            {{ $course->school->name }} i {{ $course->school->city->name }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <no-results title="Inga kurser hittades"></no-results>
        @endif
    </div>
    {!! $oldCourses->render('pagination::bootstrap-4') !!}
@endsection
