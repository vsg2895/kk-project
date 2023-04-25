@extends('organization::layouts.default')
@section('content')

    @if(!$schoolsCount)
        <div class="alert alert-danger" role="alert">
            <strong>Du måste skapa en trafikskola innan du kan lägga upp en kurs.</strong>
        </div>
    @else
        <header class="section-header">
            <nav class="page-nav">
                <a class="btn btn-sm btn-primary" href="{{ route('organization::courses.index') }}?calendar=true">Kalender
                    vy</a>
                <a class="btn btn-sm btn-primary" href="{{ route('organization::courses.create') }}">Lägg upp ny
                    kurs</a>
            </nav>
            <h1 class="page-title">Kurser</h1>
        </header>

        @include('shared::components.message')

        <div class="row pl-3 pb-1 organization-filter-row">
            <select class="organization-filter" id="organization-course-filter">
                <option value="{{ null }}" selected>Välj Kurs</option>
                @foreach($coursesVehicleSegments as $coursesVehicleSegment)
                    <option
                        @if(request()->has('vehicle_segment_id') && request()->vehicle_segment_id == $coursesVehicleSegment->id) selected
                        @endif
                        value="{{ $coursesVehicleSegment->id }}">{{ $coursesVehicleSegment->label }}</option>
                @endforeach
            </select>
            <select class="organization-filter" id="organization-school-filter">
                <option value="{{ null }}" selected>Välj Skola</option>
                @foreach($schools as $school)
                    <option
                        @if(request()->has('school_id') && request()->school_id == $school->id) selected
                        @endif
                        value="{{ $school->id }}">{{ $school->name }}</option>
                @endforeach
            </select>

            <button class="btn btn-sm btn-primary custom-organization-filter">Filter</button>
        </div>

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
                                <a class="btn btn-sm btn-outline-primary"
                                   href="{{ route('organization::courses.show', ['id' => $course->id]) }}">Visa</a>
                            </div>
                            <div class="table-cell col-md-3">
                                <a href="{{ route('organization::courses.show', ['id' => $course->id]) }}">{{ $course->start_time->formatLocalized('%A %d:e %B, %Y (%H:%M)') }}</a>
                            </div>
                            <div class="table-cell col-md-2">
                                {{ $course->name }}
                            </div>
                            <div
                                class="table-cell col-md-1 text-md-center @if(!$course->bookings->where('cancelled', false)->count()) text-muted @else text-success @endif">
                                {{ $course->bookings->where('cancelled', false)->count() }}<span class="hidden-md-up"> deltagare</span>
                            </div>
                            <div
                                class="table-cell col-md-2 text-md-center @if($course->seats > 0) text-success @else text-muted @endif">
                                {{ $course->seats }} <span class="hidden-md-up"> platser kvar</span>
                            </div>
                            <div class="table-cell col-md-4 text-md-right">
                                {{ $course->school->name }} i {{ $course->school->city->name }}
                            </div>
                        </div>
                    @endforeach
                </div>
                {!! $courses->appends($_GET)->render('pagination::bootstrap-4') !!}
            @else
                <no-results title="Inga kurser hittades"></no-results>
            @endif
        </div>

        <div class="card card-block">
            <h3>Avklarade kurser</h3>
            @if($oldCourses->count())
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
                    @foreach($oldCourses as $course)
                        <div class="table-row">
                            <div class="table-cell hidden-md-up more-button">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="{{ route('organization::courses.show', ['id' => $course->id]) }}">Visa</a>
                            </div>
                            <div class="table-cell col-md-3">
                                <a href="{{ route('organization::courses.show', ['id' => $course->id]) }}">{{ $course->start_time->formatLocalized('%A %d:e %B, %Y (%H:%M)') }}</a>
                            </div>
                            <div class="table-cell col-md-2">
                                {{ $course->name }}
                            </div>
                            <div
                                class="table-cell col-md-1 text-md-center @if(!$course->bookings->where('cancelled', false)->count()) text-muted @else text-success @endif">
                                {{ $course->bookings->where('cancelled', false)->count() }}<span class="hidden-md-up"> deltagare</span>
                            </div>
                            <div
                                class="table-cell col-md-2 text-md-center @if($course->seats > 0) text-success @else text-muted @endif">
                                {{ $course->seats }} <span class="hidden-md-up"> platser kvar</span>
                            </div>
                            <div class="table-cell col-md-4 text-md-right">
                                {{ $course->school->name }} i {{ $course->school->city->name }}
                            </div>
                        </div>
                    @endforeach
                </div>
                {!! $oldCourses->appends($_GET)->render('pagination::bootstrap-4') !!}
            @else
                <no-results title="Inga kurser hittades"></no-results>
            @endif
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('change', '.organization-filter', function () {
                var selectedVehicle = $('#organization-course-filter').find("option:selected").val();
                var selectedSchool = $('#organization-school-filter').find("option:selected").val();
                var url = new URL(window.location);

                if (selectedVehicle !== '' && selectedVehicle !== null && selectedVehicle !== undefined) {
                    url.searchParams.set('vehicle_segment_id', selectedVehicle);
                } else {
                    url.searchParams.delete('vehicle_segment_id');
                }
                if (selectedSchool !== '' && selectedSchool !== null && selectedSchool !== undefined) {
                    url.searchParams.set('school_id', selectedSchool);
                } else {
                    url.searchParams.delete('school_id');
                }

                window.history.pushState({}, '', url);
            })

            $(document).on('click', '.custom-organization-filter', function () {
                var urlNew = new URL(window.location);
                let params = new URLSearchParams(urlNew.search);
                if (params.toString().indexOf('page') !== -1) {
                    params.delete('page');
                    var baseUrl = window.location.href.split('?')[0]
                    var newUrl = baseUrl + '?' + params + '&page=1';
                    window.history.pushState({}, '', newUrl);

                }

                location.reload();
            })


            var getUrlParameter = function getUrlParameter(sParam) {
                var sPageURL = window.location.search.substring(1),
                    sURLVariables = sPageURL.split('&'),
                    sParameterName,
                    i;

                for (i = 0; i < sURLVariables.length; i++) {
                    sParameterName = sURLVariables[i].split('=');

                    if (sParameterName[0] === sParam) {
                        return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                    }
                }
                return false;
            };
        });
    </script>
@endsection
