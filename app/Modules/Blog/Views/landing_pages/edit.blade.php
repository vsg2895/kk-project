@extends('admin::layouts.default')
@section('content')

    @include('shared::components.errors')

    <div class="card card-block">
        <landing-edit-form :post-id="{{ $post->id }}"></landing-edit-form>
    </div>
@endsection
