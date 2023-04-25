@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <nav class="d-flex mb-1">
            <a class="btn btn-sm btn-primary" href="{{ route('admin::organizations.create') }}">Lägg till organisation</a>
        </nav>
        <h1 class="page-title">Organisationer</h1>
    </header>

    @if(Session::has('errors'))
        <div class="alert alert-danger">
            <strong>Organization login fail</strong><br>
            {!! Session::get('errors') !!}
        </div>
    @endif

    @include('shared::components.message')
    @include('shared::components.info')

    @component('form.search')@endcomponent

    <div class="card card-block mx-0">
        @if($organizations->count())
            <div class="table">
                <div class="table-head table-row hidden-sm-down">
                    <div class="table-cell col-md-5">
                        Namn
                    </div>
                    <div class="table-cell col-md-5">
                        Added At
                    </div>
                    <div class="table-cell col-md-1">
                        Statistik
                    </div>
                    <div class="table-cell col-md-1">
                        Login
                    </div>
                </div>
                @foreach($organizations as $organization)
                    <div class="table-row">
                        <div class="table-cell col-md-5">
                            @if($organization->deleted_at)
                                <span class="text-danger">Borttagen</span>
                            @endif
                            <a href="{{ route('admin::organizations.show', ['id' => $organization->id]) }}">
                               {{ $organization->name }}
                            </a>
                        </div>
                        <div class="table-cell col-md-5">
                            @include('shared::components.datetime', ['date' => $organization->created_at, 'showOriginal' => true])
                        </div>
                        <div class="table-cell col-md-1">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin::statistics.organizations.show', ['id' => $organization->id]) }}">Statistik</a>
                        </div>
                        <div class="table-cell col-md-1">
                            <a class="btn btn-sm btn-outline-success" href="{{ route('admin::statistics.organizations.login', ['id' => $organization->id]) }}">Login</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <no-results title="Inga organisationer hittades"></no-results>
        @endif
    </div>

    {!! $organizations->render('pagination::bootstrap-4') !!}
@endsection
