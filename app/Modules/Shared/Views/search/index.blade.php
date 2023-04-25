@extends('shared::layouts.default')
@section('pageTitle')
    {{ $title }} |
@stop
@section('body-class')
    page-search
@endsection
@section('content')
    <search-page
        :initial-city="{{ $city? $city->id : 143 }}"
        :initial-latitude="{{ $city? $city->latitude : 59.329323 }}"
        :initial-longitude="{{ $city? $city->longitude : 18.068581 }}"
        :initial-vehicle="{{ $vehicleId ?: 1 }}"
        initial-result-type="{{ $resultType?: 'SCHOOL' }}"
        :initial-course-type="{{ $courseType ?: 0 }}"
        initial-in-paket="{{ $inPaket ?? false }}"
        initial-title="{{ $title }}">
    </search-page>
    @if(isset($schools) && $schools)
        <noscript>
            <ul>
                @foreach ($schools as $school)
                    <li><a href="{{ route('shared::schools.show', ['citySlug' => $school->city->slug ,'schoolSlug' => $school->slug]) }}">{{ $school->name }}</a></li>
                @endforeach
            </ul>
        </noscript>
    @endif

{{--    @if(isset($courses) && $courses)--}}
{{--        <noscript>--}}
{{--            <ul>--}}
{{--                @foreach ($courses as $course)--}}
{{--                    <li><a href="{{ route('shared::schools.show', ['citySlug' => $course->school->city->slug ,'schoolSlug' => $course->school->slug]) }}">{{ $course->school->name }}</a></li>--}}
{{--                    <li><a href="{{ route('shared::courses.show', ['citySlug' => $course->school->city->slug ,'schoolSlug' => $course->school->slug, 'courseId' => $course->id]) }}">{{ $course->name }}</a></li>--}}
{{--                @endforeach--}}
{{--            </ul>--}}
{{--        </noscript>--}}
{{--    @endif--}}
@endsection

@section('scripts')
    @parent
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_API_KEY') }}&callback=initMap"></script>
@endsection
