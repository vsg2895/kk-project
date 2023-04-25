@extends('admin::layouts.default')
@section('body-class') page-dashboard @parent @stop

@section('content')

    <header class="section-header">
        <a class="back-button btn btn-sm btn-outline-primary" href="{{ route('admin::partners.index') }}">@icon('arrow-left')
            Tillbaka</a>
        <h1 class="page-title">Partner `<strong>{{ $partner->partner }}</strong>`</h1>
    </header>

    <div class="card card-block">
        <form method="POST" action="{{ route('admin::partners.update', $partner->id) }}" enctype="multipart/form-data">
            @include('shared::components.partners.edit')
            <button class="btn btn-success" type="submit">Updattera</button>
        </form>
    </div>

@endsection
