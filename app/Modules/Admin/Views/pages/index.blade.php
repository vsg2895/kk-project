@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <nav class="d-flex mb-1">
            <a class="btn btn-sm btn-primary" href="{{ route('admin::pages.create') }}">Ny sida</a>
        </nav>
        <h1 class="page-title">Sidor <span class="small text-muted">{{ $pages->total() }} st</span></h1>
    </header>

    <div class="card card-block mx-0">
        @if($pages->count())
            <div class="table">
                <div class="table-head table-row hidden-sm-down">
                    <div class="table-cell col-md-4">Titel</div>
                    <div class="table-cell col-md-4">Uri</div>
                    <div class="table-cell col-md-2">Publicerad</div>
                    <div class="table-cell col-md-2">Uppdaterad</div>
                </div>

                @foreach($pages as $page)
                    <div class="table-row" data-id="{{ $page->id }}">
                        <div class="table-cell hidden-md-up more-button">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin::pages.show', ['id' => $page->id]) }}">Redigera</a>
                        </div>
                        <div class="table-cell col-md-4">
                            <a href="{{ route('admin::pages.show', ['id' => $page->id]) }}">{{ $page->title }}</a>
                        </div>
                        <div class="table-cell col-md-4">
                            @if($page->uri)
                                <a href="{{ url($page->uri->uri) }}">{{ $page->uri->uri }}</a>
                            @else
                                -
                            @endif
                        </div>
                        <div class="table-cell col-md-2">
                            @include('shared::components.datetime', ['date' => $page->published_at])
                        </div>
                        <div class="table-cell col-md-2">
                            @include('shared::components.datetime', ['date' => $page->updated_at])
                        </div>
                    </div>
                @endforeach

            </div>
        @else
            <no-results title="Inga sidor hittades"></no-results>
        @endif
    </div>
    {!! $pages->render('pagination::bootstrap-4') !!}
@endsection
