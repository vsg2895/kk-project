@extends('organization::layouts.default')
@section('content')
    <header class="section-header">
        <h1 class="page-title">Betyg <span class="small text-muted">{{ $ratings->total() }} st</span></h1>
    </header>

    <div class="card card-block">
        @include('shared::components.rating.list')
    </div>

    {!! $ratings->render('pagination::bootstrap-4') !!}
@endsection
