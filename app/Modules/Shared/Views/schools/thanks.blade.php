@extends('shared::layouts.default')
@section('pageTitle')
    {{ $title }}
@stop
@section('content')
    <div id="booking-confirmation" class="container text-xs-center" style="padding-top: 150px">
        @if(isset($school))
            <h1 class="card-title">Tack för ditt omdöme {{ $user->getNameAttribute() }}</h1>
            <p>Vi vill tacka dig som tagit dig tid att ge ett omdöme kring utbildningen på vår trafikskola!</p>
        @endif
    </div>
@endsection
