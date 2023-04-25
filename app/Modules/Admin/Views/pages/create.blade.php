@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('admin::pages.index') !!}">@icon('arrow-left') Tillbaka</a>
        <h1 class="page-title">Ny sida</h1>
    </header>

    @include('shared::components.errors')
    <div class="card card-block">
        <form method="POST" action="{{ route('admin::pages.store') }}">
            @include('admin::pages.form', ['page' => $page])
        </form>
    </div>
@endsection
