@extends('admin::layouts.default')
@section('body-class') page-dashboard @parent @stop

@section('content')

    <header class="section-header">
        <a class="back-button btn btn-sm btn-outline-primary" href="{{ route('admin::course_type.index') }}">@icon('arrow-left')
            Tillbaka</a>
        <h1 class="page-title">Course Type</h1>
    </header>

    <div class="card card-block">
        <form method="POST" action="{{ route('admin::course_type.store') }}" enctype="multipart/form-data">
            @include('shared::components.course_type.create')
            <button class="btn btn-success" type="submit">Skapa</button>
        </form>
    </div>

@endsection
