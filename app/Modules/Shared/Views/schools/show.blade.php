@extends('shared::layouts.default')
@section('pageTitle')
    {{ $title }}
@stop
@section('content')
    <school-page
            @if(auth()->check())
                :user="{{ json_encode(auth()->user()->load('ratings')) }}"
            @endif
            :id="{{ $school->id }}"
            booking-fee="{{ config('fees.booking_fee_to_kkj') }}"
    ></school-page>
    <noscript>
        @foreach($school->upcomingCourses as $course)
            <a href="{{ route('shared::courses.show', ['citySlug' => $school->city->slug, 'schoolSlug' => $school->slug, 'courseId' => $course->id]) }}">{{ $course->name }}</a>
        @endforeach
    </noscript>
@endsection
@section('scripts')
    @parent
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_API_KEY') }}&callback=initMap"></script>
    {!! $localBusiness  !!}
@endsection
