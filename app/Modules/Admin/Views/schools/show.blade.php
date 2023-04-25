@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <nav class="d-flex justify-content-between">
            <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('admin::schools.index') !!}">@icon('arrow-left') Tillbaka</a>
            @if(!$school->deleted_at)
            <form method="POST" action="{{ route('admin::schools.delete', ['id' => $school->id]) }}">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <button type="submit" class="btn btn-sm btn-outline-danger ml-1" data-confirm="@lang('form.confirm_action')">Ta bort trafikskola</button>
            </form>
            @endif
        </nav>

        <h1 class="page-title">{{ $school->name }}
            @if($school->deleted_at)
                <span class="text-danger">Skolan har tagits bort</span>
            @else
                <a class="small text-muted" href="{{ route('shared::schools.show', ['citySlug' => $school->city->slug, 'schoolSlug' => $school->slug]) }}" target="_blank">Besök skolsida @icon('arrow-right')</a>
            @endif
        </h1>
    </header>

    @include('shared::components.message')
    @include('shared::components.errors')

    <div class="card card-block mx-0">
        <loyalty-progress :schools="{{ collect([$school]) }}" :loyalty-levels="{{ $loyaltyLevels }}" />
    </div>

    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active mx-0" data-toggle="tab" href="#tab-info" role="tab">Skolans detaljer</a>
        </li>
        @if(!$school->deleted_at)
            <li class="nav-item">
                <a class="nav-link mx-0" data-toggle="tab" href="#tab-prices" role="tab">Paketpriser</a>
            </li>
            <li class="nav-item">
                <a class="nav-link mx-0" data-toggle="tab" href="#tab-fees" role="tab">Redigera fees</a>
            </li>
            <li class="nav-item">
                <a class="nav-link mx-0" data-toggle="tab" href="#tab-courses" role="tab">Kurser</a>
            </li>
            <li class="nav-item">
                <a class="nav-link mx-0" data-toggle="tab" href="#tab-comments" role="tab">
                    Kommentarer <span class="tag tag-default tag-pill">{{ count($school->comments) }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mx-0" data-toggle="tab" href="#tab-gallery" role="tab">Gallery</a>
            </li>
        @endif
    </ul>
    <div class="card card-block mx-0">
        <div class="tab-content">
            @include('shared::components.school.edit', ['type' => 'admin'])

            <div class="tab-pane" id="tab-comments" role="tabpanel">
                <h3>Kommentera</h3>

                @include('admin::components.annotation.form', [
                    'type' => 'user.school.comment',
                    'action' => route('admin::schools.create.comment', ['id' => $school->id]) . '#comments'
                ])

                <hr>

                @include('admin::components.annotation.list', ['list' => $school->comments])
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_API_KEY') }}&libraries=places&language=sv&region=se"></script>
@endsection
