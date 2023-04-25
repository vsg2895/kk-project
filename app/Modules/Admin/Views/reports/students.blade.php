@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <a class="back-button btn btn-sm btn-outline-primary" href="{{ route('admin::reports.index') }}">@icon('arrow-left') Tillbaka</a>
        <h1 class="page-title">Elever</h1>
        <div class="text-sm-right">
            <a href="{{ route('admin::reports.students.export') }}" class="btn btn-primary">Exportera</a>
        </div>

    </header>
    @include('shared::components.message')
    @include('shared::components.errors')

    <div class="card card-block">
        @if($students->count())
            <div class="table">
                <div class="table-head table-row hidden-sm-down">
                    <div class="table-cell col-md-3">#</div>
                    <div class="table-cell col-md-3">Namn</div>
                    <div class="table-cell col-md-3">E-post</div>
                    <div class="table-cell col-md-3">Datum</div>
                </div>
                @foreach($students as $student)
                    <div class="table-row">
                        <div class="table-cell col-md-3">{{ $student->id }}</div>
                        <div class="table-cell col-md-3">{{ $student->name }}</div>
                        <div class="table-cell col-md-3">{{ $student->email }}</div>
                        <div class="table-cell col-md-3">{{ $student->created_at }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <no-results title="Beställningar hittades inte"></no-results>
        @endif
    </div>

@endsection
