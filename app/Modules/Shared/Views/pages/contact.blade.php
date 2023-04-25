@extends('shared::layouts.default')
@section('robots')
    <meta name="robots" content="noindex">
@stop
@section('pageTitle')
    Kontakta oss |
@stop
@section('content')
    <contact-page subject="{{ $subject }}" school-id="{{ $school }}"></contact-page>
@endsection