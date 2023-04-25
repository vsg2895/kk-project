@extends('admin::layouts.default')
@section('content')

    @include('shared::components.errors')

    <div class="card card-block">
        <post-create-form></post-create-form>
    </div>
@endsection
