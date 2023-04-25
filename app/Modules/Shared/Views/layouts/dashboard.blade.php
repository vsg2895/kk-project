@extends('shared::layouts.master')
@section('body-class') dashboard @stop

@section('main')
    <aside id="page-sidebar" class="hidden-md-down left-navigation">
        <nav class="nav nav-stacked">
            @yield('sidebar-nav')
        </nav>
    </aside>

    <section id="page-content">
        <div class="container-fluid">
            @if(Session::has('klarna_errors'))
                <div class="alert alert-danger">
                    <strong>Klarna Debug Information</strong><br>
                    {!! Session::get('klarna_errors')!!}
                </div>
            @endif

            @yield('content')
        </div>
    </section>
@endsection
