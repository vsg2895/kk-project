@extends('admin::layouts.default')
@section('body-class') page-dashboard @parent @stop

@section('content')

    <header class="section-header">
        <nav class="d-flex">
            <a class="back-button btn btn-sm btn-outline-primary" href="{{ route('admin::partners.index') }}">
                @icon('arrow-left')
                <span>Tillbaka</span>
            </a>
        </nav>
        <h1 class="page-title">Partners</h1>
    </header>

    <div class="card card-block mx-0">
        <form method="POST" action="{{ route('admin::partners.store') }}" enctype="multipart/form-data">
            @include('shared::components.partners.create')
            <button class="btn btn-success" type="submit">Skapa</button>
        </form>
    </div>

@endsection
