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
    @if($klarnaOrder)
        <course-payment-page @if($klarnaOrder) klarna-order-id="{{ $klarnaOrder['id'] }}" @endif ></course-payment-page>
    @endif
@endsection

@section('no-vue')
    <div class="checkout-container  disabled">
        @if($klarnaOrder)
            {!! $klarnaOrder['html_snippet'] !!}
        @endif
    </div>
@endsection
