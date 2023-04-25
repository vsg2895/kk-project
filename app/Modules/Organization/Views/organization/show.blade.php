@extends('organization::layouts.default')
@section('content')
    <header class="section-header">
        <h1 class="page-title">{{ $organization->name }}</h1>
    </header>
    @include('shared::components.message')
    @include('shared::components.errors')
    <div class="card card-block col-lg-6 col-xl-4 mx-0">
        <form method="POST" action="{{ route('organization::organization.update') }}" enctype="multipart/form-data">
            @include('shared::components.organization.edit')
                <button type="submit" class="btn btn-success">Spara</button>
        </form>
    </div>
@endsection
