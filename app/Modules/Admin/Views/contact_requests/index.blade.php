@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <h1 class="page-title">Kontakta oss <span class="small text-muted">{{ $requests->total() }} st</span></h1>
    </header>

    <div class="card card-block">
        @if($requests->count())
            <div class="table">
                <div class="table-head table-row">
                    <div class="table-cell col-md-3">Namn</div>
                    <div class="table-cell col-md-4">E-post</div>
                    <div class="table-cell col-md-3">Titel</div>
                    <div class="table-cell col-md-2">Skapad</div>
                </div>

                @foreach($requests as $request)
                    <div class="table-row" data-id="{{ $request->id }}">
                        <div class="table-cell col-md-3">
                            <a href="{{ route('admin::contact_request.show', ['id' => $request->id]) }}">{{ $request->name }}</a>
                        </div>
                        <div class="table-cell col-md-4">
                            <a href="{{ route('admin::contact_request.show', ['id' => $request->id]) }}">{{ $request->email }}</a>
                        </div>
                        <div class="table-cell col-md-3">
                            <a href="{{ route('admin::contact_request.show', ['id' => $request->id]) }}">{{ $request->title }}</a>
                        </div>
                        <div class="table-cell col-md-2">
                            @include('shared::components.datetime', ['date' => $request->created_at])
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <no-results title="Inga meddelanden hittades"></no-results>
        @endif
    </div>
    {!! $requests->render('pagination::bootstrap-4') !!}
@endsection
