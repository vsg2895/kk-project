@extends('organization::layouts.default')
@section('content')
    <header class="section-header">
        <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('organization::users.index') !!}">@icon('arrow-left') Tillbaka</a>
        <h1 class="page-title">Ny användare</h1>
    </header>

    @include('shared::components.errors')

    <div class="card card-block">
        <form method="POST" action="{{ route('organization::users.store') }}">
            @include('shared::components.user.create', ['type' => 'organization'])
            {{ csrf_field() }}
            <button class="btn btn-success" type="submit">Skapa</button>
        </form>
    </div>
@endsection