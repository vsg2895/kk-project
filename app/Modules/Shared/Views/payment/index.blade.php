@extends('shared::layouts.default', ['checkoutPage' => 'checkout-page'])
@section('pageTitle')
    | Boka
@stop
@section('body-class')
    page-course
@endsection
@section('content')
    <payment-page
        @if($klarnaOrder) klarna-order-id="{{ $klarnaOrder['id'] }}" @endif
        :course-ids="{!! $courseIds !!}"
        :addon-ids="{!! $addonIds !!}"
        :custom-ids="{!! $customIds !!}"
        :booking-fee="{{ config('fees.booking_fee_to_kkj') }}"
        :school-id="{{ $schoolId }}">
    </payment-page>
@endsection
@section('scripts')
    @parent
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_API_KEY') }}"></script>
    {!! $schemaCourse  !!}
@endsection
@section('no-vue')
    <div class="checkout-container disabled">
        @if($klarnaOrder) {!! $klarnaOrder['html_snippet'] !!} @endif
    </div>
@endsection
