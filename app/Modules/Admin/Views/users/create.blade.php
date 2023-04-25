@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('admin::users.index') !!}">@icon('arrow-left') Tillbaka</a>
        <h1 class="page-title">Ny användare</h1>
    </header>

    @include('shared::components.errors')

    <div class="card card-block">
        <form id="user-create" method="POST" action="{{ route('admin::users.store') }}">
            {{ csrf_field() }}
            @include('shared::components.user.create')
            <button class="btn btn-success" type="submit">Skapa</button>
        </form>
    </div>
@endsection
