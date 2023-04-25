@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('admin::contact_request.index') !!}">@icon('arrow-left') Tillbaka</a>
        <h1 class="page-title">Meddelande från {{ $request->name }}</h1>
        <p><span class="tag tag-pill tag-default">{{ $request->created_at->formatLocalized('%A %d:e %B, %Y (%H:%M)') }}</span></p>
    </header>

    <div class="card card-block">
        <form><fieldset disabled>
            <div class="form-group">
                <label for="title">Titel</label>
                <input class="form-control" id="title" name="title" aria-describedby="titleHelp" value="{{ $request->title }}">

                @if($request->school_id)
                    <p id="titleHelpBlock" class="form-text text-muted">
                        <a href="{{ route('admin::schools.show', ['id' => $request->school->id]) }}">
                            @icon('arrow-right') {{ $request->school->name }}
                        </a>
                    </p>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Namn</label>
                <input class="form-control" id="name" name="name" aria-describedby="nameHelp" value="{{ $request->name }}">
            </div>

            <div class="form-group">
                <label for="email">E-post</label>
                <input class="form-control" id="email" name="email" aria-describedby="emailHelp" value="{{ $request->email }}">
            </div>

            <div class="form-group">
                <label for="message">Innehåll</label>
                <textarea class="form-control" aria-describedby="messageHelp" id="message" name="message" rows="3">{{ $request->message }}</textarea>
            </div>
        </fieldset></form>
    </div>
@endsection
