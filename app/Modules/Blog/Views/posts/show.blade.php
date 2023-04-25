@extends('shared::layouts.default')

<style>
    @media only screen and (max-height: 480px) {
        .content-container {
            display: flex;
            justify-content: space-between;
        }
    }
</style>

@section('content')
    <div class="container">
        <div class="content-container">
            <div class="post-card col-12 col-md-8">
                <h1 class="post-card__title">{{ $post->title }}</h1>

                <div class="post-card__meta">
                    <span class="time">{{ date('d M, Y', $post->created_at->timestamp) }}</span>
                </div>

                <div class="post-card__body">
                    @if(!is_null($post->preview_img_filename))
                        <div class="post-img">
                            <img class="image" src="{{ $post->previewImgFilenameUrl }}" alt="">
                        </div>
                    @endif

                    <div class="post-card__content">
                        {!! $post->content !!}
                    </div>
                    <div class="post-card__footer_content mt-3">
                        {!! $post->footer_content !!}
                        @if($post->button_text && $post->link)
                            <div>
                                <a href="{{$post->link}}" class="btn btn-success" style="max-width: fit-content;">{{$post->button_text}}</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4" style="padding: 95px 15px;">
                <h2>Senaste blogginlägg</h2>
                <posts-page-show :role-id="{{ Auth::check() ? Auth::user()->role_id : 0 }}"></posts-page-show>
            </div>
        </div>
    </div>
@endsection

@if($post->hidden)
<script>
    var meta = document.createElement('meta');
    meta.name = "robots";
    meta.content = "noindex";
    document.getElementsByTagName('head')[0].appendChild(meta);
</script>
@endif
