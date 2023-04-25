@extends('shared::layouts.default')
@section('pageTitle', 'Logga in |')
@section('body-class')
    page-auth
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                @include('shared::components.message')

                <h1 class="logga-in">Logga in</h1>
                <login-page errors="{{ $errors->toJson() }}" csrf-token="{{ csrf_token() }}" old-email="{{ old('email') }}" ></login-page><hr>
                <p>Inte medlem ännu?</p>
                <a class="btn btn-black" href="{{ route('auth::register.show') }}">Registrera dig</a>
            </div>
        </div>
    </div>
    <div class="license-back"></div>
@endsection
