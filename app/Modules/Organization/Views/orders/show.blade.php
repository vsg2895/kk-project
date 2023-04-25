@extends('organization::layouts.default')
@section('content')
   <order-page-organization :order-id="{{ $orderId }}"></order-page-organization>
@endsection
