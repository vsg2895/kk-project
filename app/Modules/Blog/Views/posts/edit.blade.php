@extends('admin::layouts.default')
@section('content')

    @include('shared::components.errors')

    <div class="card card-block">
        <post-edit-form :post-id="{{ $post->id }}"></post-edit-form>
    </div>
@endsection