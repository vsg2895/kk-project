@extends('admin::layouts.default')
@section('content')
    <header class="section-header ml-1">
        <div class="d-flex justify-content-start mb-1">
            <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('admin::courses.index') !!}">@icon('arrow-left')Tillbaka</a>
        </div>
        <h1 class="page-title">
            {{ $course->name }}
            @if($course->deleted_at)
                <span class="text-danger">Kursen är borttagen</span>
            @endif
        </h1>
    </header>

    @include('shared::components.message')
    @include('shared::components.errors')

    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#tab-info" role="tab">
                Kursinformation
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab-bookings" role="tab">
                Bokningar
                <span class="tag tag-pill @if($course->bookings->where('cancelled', false)->count()) tag-success @else tag-default @endif">
                    {{ $course->bookings->where('cancelled', false)->count() }}
                </span>
            </a>
        </li>
    </ul>

    <div class="card card-block">
        <div class="tab-content">
            <div class="tab-pane active" id="tab-info" role="tabpanel">
                <form method="POST" action="{{ route('admin::courses.update', $course->id) }}#info">
                    {{ csrf_field() }}
                    <course-form :initial-course="{{ json_encode($course) }}" :old-data="{{ json_encode(old()) }}"
                                 :is-admin="true"></course-form>

                    @if(!$course->deleted_at)
                        <button type="submit" class="btn btn-success">Spara</button>
                    @endif
                </form>

                <div class="d-flex justify-content-start">
                    <a href="{{ route('admin::courses.create', ['initialCourse' => $course->id]) }}"
                    class="btn btn-primary mt-1">
                        Kopiera kurs
                    </a>
                </div>
                <form class="mt-1" method="POST" action="{{ route('admin::courses.delete', $course->id) }}">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="submit" class="btn btn-outline-danger" data-confirm="@lang('form.confirm_action')">
                        Ta bort
                    </button>
                </form>
            </div>

            <div class="tab-pane" id="tab-bookings" role="tabpanel">
                <h3>
                    Bokningar
                    @if($course->bookings->where('cancelled', false)->count())
                        <a id="href-inv" style="margin-right: 5px" target="_blank" href="{{ route('admin::courses.download.participants', ['id' => $course->id]) }}"
                           class="btn btn-sm btn-primary pull-right">Ladda ner deltagarlista
                        </a>@endif
                </h3>
                <div class="table">
                    <div class="table-head table-row">
                        <div class="table-cell col-md-4">
                            Deltagare
                        </div>
                        <div class="table-cell col-md-3">
                            Typ
                        </div>
                        <div class="table-cell col-md-3">
                            Betalningssätt
                        </div>
                        <div class="table-cell col-md-2">
                        </div>
                    </div>
                    @foreach($course->bookings->where('cancelled', false)->groupBy('order_id') as $bookingGroup)
                        <div class="table-row">
                            <div class="table-cell col-md-4">
                                @foreach($bookingGroup as $booking)
                                    <p>{{ $booking->participant->name }}</p>
                                @endforeach
                            </div>
                            <div class="table-cell col-md-3">
                                @foreach($bookingGroup as $booking)
                                    <p>{{ trans('courses.' . strtolower($booking->participant->type)) }}</p>
                                @endforeach
                            </div>
                            <div class="table-cell col-md-3">
                                {{ trans('payment.types.'.$bookingGroup->first()->order->payment_method) }}
                            </div>
                            <div class="table-cell col-md-2">
                                <a href="{{ route('admin::orders.show', ['id' => $bookingGroup->first()->order_id]) }}"
                                   class="btn btn-sm btn-primary">
                                    Visa beställning
                                </a>
                            </div>
                        </div>
                    @endforeach
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
