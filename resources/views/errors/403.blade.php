@extends('shared::layouts.default')
@section('body-class')
    page-error
@endsection
@section('content')
    <div class="error-403">
        <div class="container">
            <div class="error-number text-numerical">403</div>

            <h1>Åtkomst nekad @icon('smiley-2', 'xl')</h1>
            <p class="lead">Du har inte nödvändiga rättigheter för att visa denna sida.</p>

            <a class="btn btn-sm btn-outline-primary" href="{{ route('auth::login') }}">Logga in</a>
        </div>
    </div>
@endsection
