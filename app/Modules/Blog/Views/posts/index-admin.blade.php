@extends('admin::layouts.default')
@section('content')

    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#tab-posts" role="tab">
                Posts
            </a>
        </li>
{{--        <li class="nav-item">--}}
{{--            <a class="nav-link" data-toggle="tab" href="#tab-pages" role="tab">--}}
{{--                Pages--}}
{{--            </a>--}}
{{--        </li>--}}
    </ul>

    <div class="card card-block">
        <div class="tab-content">
            <div class="tab-pane active" id="tab-posts" role="tabpanel">
                <a class="btn btn-sm btn-primary" href="{{ route('blog::posts.create') }}">Create post</a>

                <div class="card card-block admin-blog-list mt-2">
                    @if($posts->count())
                        <div class="table">
                            <div class="table-head table-row hidden-sm-down">
                                <div class="table-cell col-md-2">
                                    @sortablelink('title')
                                </div>
                                <div class="table-cell col-md-2">
                                    @sortablelink('status')
                                </div>
                                <div class="table-cell col-md-3">
                                    @sortablelink('user_id', 'Author')
                                </div>
                                <div class="table-cell col-md-2">
                                    @sortablelink('created_at', 'Created At')
                                </div>

                                <div class="table-cell col-md-3">
                                    Action
                                </div>
                            </div>
                            @foreach($posts as $post)
                                <div class="table-row">
                                    <div class="table-cell col-md-2">
                                        <a href="{{ route('blog::show', $post->slug) }}">
                                            <span class="post-title">{{ $post->title }}</span>
                                        </a>
                                    </div>

                                    <div class="table-cell col-md-2">
                                        <span class="post-title">{{ $post->status ? 'visible' : 'hidden' }}</span>
                                    </div>

                                    <div class="table-cell col-md-3">
                                        {{ $post->user->name }}
                                    </div>

                                    <div class="table-cell col-md-2">
                                        {{ $post->created_at }}
                                    </div>

                                    <div class="table-cell col-md-3 pt-0 pb-0">
                                        <div class="admin-blog-list__btn-box">
                                            <a href="{{ route('blog::posts.edit', $post->id  ) }}"
                                               class="bnt btn-primary btn-sm">Edit</a>
                                            <form action="{{ route('blog::posts.delete', $post->id) }}" method="POST">
                                                {{ csrf_field() }}

                                                <input type="hidden" name="_method" value="DELETE"/>
                                                <button type="submit" class="bnt btn-danger btn-sm" data-confirm="@lang('form.confirm_action')">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <no-results title="There are no posts"></no-results>
                    @endif
                </div>
                {!! $posts->appends(Request::except('page'))->render('pagination::bootstrap-4') !!}
            </div>

            <div class="tab-pane" id="tab-pages" role="tabpanel">

{{--                <a class="btn btn-sm btn-primary" href="{{ route('blog::posts.create', ['landing' => true]) }}">Create post</a>--}}

                <div class="card card-block admin-blog-list mt-2">
                    @php
                        $landingPages = \Jakten\Models\Post::whereHas('user')->sortable([
            'created_at' => 'desc'
        ])->where('post_type', 'landing')->get();
                    @endphp
                    @if($landingPages->count())
                        <div class="table">
                            <div class="table-head table-row hidden-sm-down">
                                <div class="table-cell col-md-2">
                                    @sortablelink('title')
                                </div>
                                <div class="table-cell col-md-2">
                                    @sortablelink('status')
                                </div>
                                <div class="table-cell col-md-3">
                                    @sortablelink('user_id', 'Author')
                                </div>
                                <div class="table-cell col-md-2">
                                    @sortablelink('created_at', 'Created At')
                                </div>

                                <div class="table-cell col-md-3">
                                    Action
                                </div>
                            </div>
                            @foreach($landingPages as $post)
                                <div class="table-row">
                                    <div class="table-cell col-md-2">
                                        <a href="{{ route('blog::landing.show', $post->slug) }}">
                                            <span class="post-title">{{ $post->title }}</span>
                                        </a>
                                    </div>

                                    <div class="table-cell col-md-2">
                                        <span class="post-title">{{ $post->status ? 'visible' : 'hidden' }}</span>
                                    </div>

                                    <div class="table-cell col-md-3">
                                        {{ $post->user->name }}
                                    </div>

                                    <div class="table-cell col-md-2">
                                        {{ $post->created_at }}
                                    </div>

                                    <div class="table-cell col-md-3 pt-0 pb-0">
                                        <div class="admin-blog-list__btn-box">
                                            <a href="{{ route('blog::posts.edit', ['post' => $post->id, 'landing' => true]  ) }}"
                                               class="bnt btn-primary btn-sm">Edit</a>
                                            <form action="{{ route('blog::posts.delete', $post->id) }}" method="POST">
                                                {{ csrf_field() }}

                                                <input type="hidden" name="_method" value="DELETE"/>
                                                <button type="submit" class="bnt btn-danger btn-sm" data-confirm="@lang('form.confirm_action')">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <no-results title="There are no landing pages"></no-results>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
