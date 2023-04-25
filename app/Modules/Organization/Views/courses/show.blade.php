@extends('organization::layouts.default')
@section('content')
    <header class="section-header ml-1">
        <div class="d-flex justify-content-start mb-1">
            <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('organization::courses.index') !!}">@icon('arrow-left') Tillbaka</a>
        </div>
        <h1 class="page-title">Kurs - {{ $course->name }}</h1>
    </header>

    @include('shared::components.message')
    @include('shared::components.errors')

    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#tab-bookings" role="tab">
                Bokningar
                <span class="tag tag-pill @if($course->bookings->where('cancelled', false)->count()) tag-success @else tag-default @endif">
                    {{ $course->bookings->where('cancelled', false)->count() }}
                </span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab-info" role="tab">
                Kursinformation
            </a>
        </li>
    </ul>

    <div class="card card-block">
        <div class="tab-content">
            <div class="tab-pane" id="tab-info" role="tabpanel">
                <form method="POST" action="{{ route('organization::courses.update', $course->id) }}#info">
                    {{ csrf_field() }}
                    <course-form :initial-course="{{ json_encode($course) }}"
                                 :old-data="{{ json_encode(old()) }}"></course-form>
                    <button @if($course->digital_shared && in_array($course->vehicle_segment_id, \Jakten\Models\VehicleSegment::SHARED_COURSES)) disabled @endif type="submit" class="btn btn-success">Spara</button>
                </form>
                @if(!$course->digital_shared || !in_array($course->vehicle_segment_id, \Jakten\Models\VehicleSegment::SHARED_COURSES))
                <div class="d-flex justify-content-start">
                    <a href="{{ route('organization::courses.create', ['initialCourse' => $course->id]) }}"
                    class="btn btn-primary mt-1">
                        Kopiera kurs
                    </a>
                </div>
                @endif
                <form class="mt-1" method="POST" action="{{ route('organization::courses.delete', $course->id) }}">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}

                    <button type="submit" class="btn btn-outline-danger" data-confirm="@lang('form.confirm_action')">
                        Ta bort kurs
                    </button>
                </form>
            </div>

            <div class="tab-pane active" id="tab-bookings" role="tabpanel">
                <h3>
                    Bokningar @if($course->bookings->where('cancelled', false)->count())<a id="href-inv" style="margin-right: 5px" target="_blank" href="{{ route('organization::courses.download.participants', ['id' => $course->id]) }}" class="btn btn-sm btn-primary pull-right">Ladda ner deltagarlista</a>@endif
                </h3>
                <div class="table">
                    <div class="table-head table-row hidden-sm-down">
                        <div class="table-cell col-md-4">
                            Deltagare
                        </div>
                        <div class="table-cell col-md-2">
                            Typ
                        </div>
                        <div class="table-cell col-md-2">
                            Status
                        </div>
                        <div class="table-cell col-md-2">
                            Betalningssätt
                        </div>
                        <div class="table-cell col-md-2">
                        </div>
                    </div>
                    @if($course->bookings->where('cancelled', false)->count())
                        @foreach($course->bookings->where('cancelled', false)->groupBy('order_id') as $bookingGroup)
                            <div class="table-row">
                                <div class="table-cell col-md-4">
                                    @foreach($bookingGroup as $booking)
                                        <p>{{ $booking->participant->name }}</p>
                                    @endforeach
                                </div>
                                <div class="table-cell col-md-2">
                                    @foreach($bookingGroup as $booking)
                                        <p>{{ trans('courses.' . strtolower($booking->participant->type)) }}</p>
                                    @endforeach
                                </div>
                                <div class="table-cell col-md-2">
                                    @if($bookingGroup->first()->order->cancelled)
                                        <span class="text-danger">Avbokad</span>
                                    @elseif($bookingGroup->first()->order->handled)
                                        <span class="text-success">Hanterad</span>
                                    @else
                                        <span>Artiklar kvar att hantera</span>
                                    @endif
                                </div>
                                <div class="table-cell col-md-2">
                                    {{ trans('payment.types.'.$bookingGroup->first()->order->payment_method) }}
                                </div>
                                <div class="table-cell col-md-2">
                                    <a href="{{ route('organization::orders.show', ['id' => $bookingGroup->first()->order_id]) }}"
                                       class="btn btn-sm btn-primary">
                                        Visa beställning
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <no-results title="Det finns inga bokningar ännu"></no-results>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_API_KEY') }}&libraries=places&language=sv&region=se"></script>
@endsection
