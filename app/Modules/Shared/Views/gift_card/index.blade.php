@extends('shared::layouts.default')
@section('pageTitle', 'Presentkort | Årets present |')
@section('body-class', 'gift-card')
@section('content')
<gift-card @if($klarnaOrder) klarna-order-id="{{ $klarnaOrder['order_id'] }}" @endif :bonus="{{ $bonus }}"></gift-card>
@endsection
@section('no-vue')
<script>
</script>
    <div class="checkout-container disabled">
        @if($klarnaOrder) {!! $klarnaOrder['html_snippet'] !!} @endif
    </div>
@endsection
