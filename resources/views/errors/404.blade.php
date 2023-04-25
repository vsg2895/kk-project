@extends('shared::layouts.default')
@section('body-class')
    page-error
@endsection
@section('content')
    <div class="error-404">
        <div class="container">
            <div class="error-number text-numerical">404</div>

            <h1>Sidan hittades inte @icon('smiley-2', 'xl')</h1>
            <p class="lead">Sidan du söker finns inte längre här. Kontrollera att länken är korrekt eller gå till startsidan</p>

            <p>Du kan söka i en stad eller efter trafikskola uppe i sidhuvudet &uarr;</p>
        </div>
    </div>
@endsection
