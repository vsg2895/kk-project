@extends('admin::layouts.default')
@section('content')

    @include('shared::components.errors')

    <div class="card card-block">
        <landing-create-form></landing-create-form>
    </div>
@endsection
