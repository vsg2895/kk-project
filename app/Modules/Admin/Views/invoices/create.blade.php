@extends('admin::layouts.default')
@section('content')
    <invoice-create order="{{ json_encode($order) }}"></invoice-create>
@endsection