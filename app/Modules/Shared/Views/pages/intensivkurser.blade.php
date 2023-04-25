@extends('shared::layouts.default')

@section('content')
    @if (isset($city))
        <intensive-page :initial-city={{ $city }}></intensive-page>
    @else
        <intensive-page></intensive-page>
    @endif
@endsection
