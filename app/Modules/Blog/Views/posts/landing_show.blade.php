@extends('shared::layouts.default')

@section('content')
    <div class="container">
        <div class="post-card">
            <h1 class="post-card__title">{{ $post->title }}</h1>
            <div class="post-card__body">
                <div class="post-card__content">
                    {!! $post->content !!}
                </div>
                <div class="post-card__footer_content mt-3">
                    {!! $post->footer_content !!}
                </div>
            </div>
        </div>
    </div>
@endsection
