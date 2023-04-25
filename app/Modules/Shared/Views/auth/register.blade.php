@extends('shared::layouts.default')
@section('pageTitle', 'Skapa konto -')
@section('body-class', 'page-auth')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-lg-8 offset-lg-2 mb-2">
                <h1 class="mb-2">Registrera dig som</h1>

                <a class="btn btn-black d-flex align-center mb-1" href="{{ route('auth::register.student') }}">
                    @icon('user')
                    <span class="ml-1">Elev</span>
                </a>
                <a class="btn btn-black d-flex align-center" href="{{ route('auth::register.organization') }}">
                    @icon('school')
                    <span class="ml-1">Organisation</span>
                </a>
            </div>
            <img class="road-img" src="{{ asset('build/svg/road-menu-small.svg') }}" />
        </div>
    </div>
@endsection
