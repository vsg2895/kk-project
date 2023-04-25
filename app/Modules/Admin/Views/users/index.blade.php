@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <nav class="d-flex mb-1">
            <a class="btn btn-sm btn-primary" href="{{ route('admin::users.create') }}">Skapa användare</a>
        </nav>
        <h1 class="page-title">Användare</h1>
    </header>

    @include('shared::components.message')
    @include('shared::components.info')

    @component('form.search')@endcomponent

    <div class="card card-block mx-0">
        @if($users->count())
            <div class="table">
                <div class="table-head table-row hidden-md-down">
                    <div class="table-cell col-md-4">
                        Namn
                    </div>
                    <div class="table-cell col-md-4">
                        Användartyp
                    </div>
                    <div class="table-cell col-md-4">
                        Saldo
                    </div>
                </div>
                @foreach($users as $user)
                    <div class="table-row">
                        <div class="table-cell col-md-4">
                            @if($user->deleted_at)
                                <span class="text-danger">Borttagen</span>
                            @endif
                            <a href="{{ route('admin::users.show', ['id' => $user->id]) }}">{{ $user->name ?: $user->email }}</a>
                        </div>
                        <div class="table-cell hidden-md-up more-button">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin::users.show', ['id' => $user->id]) }}">Visa</a>
                        </div>
                        <div class="table-cell col-md-4">
                            <span>{{ $user->getRoleName() }}</span>
                        </div>
                        <div class="table-cell col-md-4">
                            <span>{{ $user->amount ?: 0 }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <no-results title="Inga användare hittades"></no-results>
        @endif
    </div>

    {!! $users->render('pagination::bootstrap-4') !!}
@endsection
