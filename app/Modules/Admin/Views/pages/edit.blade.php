@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('admin::pages.index') !!}">@icon('arrow-left')
            Tillbaka</a>
        <h1 class="page-title">{{ $page->title }}</h1>
        <p><a href="{{ url('/') . $page->getUri() }}" target="_blank"
              class="tag tag-pill tag-default">{{ url('/') . $page->getUri() }}</a></p>
    </header>

    @include('shared::components.message')
    @include('shared::components.errors')

    <div class="card card-block">
        <form method="POST" action="{{ route('admin::pages.update', ['id' => $page->id]) }}">
            @include('admin::pages.form', ['page' => $page])
        </form>
    </div>
@endsection

@section('scripts')
    <script lang="text/javascript">
        let editor = CodeMirror.fromTextArea(document.getElementById('content'), {
            lineNumbers: true,
            mode: "htmlmixed",
            indentWithTabs: true,
            viewportMargin: 25,

        });
    </script>
@endsection
