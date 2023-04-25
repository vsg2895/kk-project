@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <a class="back-button btn btn-sm btn-outline-primary" href="{{ route('admin::organizations.index') }}">@icon('arrow-left') Tillbaka</a>
        <h1 class="page-title">Ny organisation</h1>
    </header>

    @include('shared::components.errors')

    <div class="card card-block">
        <form method="POST" action="{{ route('admin::organizations.store') }}" enctype="multipart/form-data">
            @include('shared::components.organization.create')
            <button class="btn btn-success" type="submit">Skapa</button>
        </form>
    </div>
@endsection
