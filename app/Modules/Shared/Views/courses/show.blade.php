@extends('shared::layouts.default')
@section('robots')
    <meta name="robots" content="noindex">
@stop
@section('pageTitle')
    | Boka {{$course->segment->label}}
@stop
@section('body-class')
    page-course
@endsection
@section('content')
    <course-page @if($klarnaOrder) klarna-order-id="{{ $klarnaOrder['id'] }}" @endif booking-fee="{{ config('fees.booking_fee_to_kkj') }}" ></course-page>
@endsection
@section('scripts')
    @parent
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_API_KEY') }}"></script>
    {!! $schemaCourse !!}
@endsection
