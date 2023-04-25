@extends('organization::layouts.default')
@section('content')
    <header class="section-header">
        <nav class="page-nav">
            <a class="btn btn-sm btn-primary" href="{{ route('organization::users.create') }}">Lägg till en ny användare</a>
        </nav>
        <h1 class="page-title">Användare</h1>
    </header>

    @include('shared::components.message')

    <div class="card card-block mx-0">
        <div class="table">
            <div class="table-head table-row hidden-sm-down">
                <div class="table-cell col-md-12">
                    Namn
                </div>
            </div>
            @foreach($users as $user)
                <div class="table-row">
                    <div class="table-cell col-md-12">
                        <a href="{{ route('organization::user.show', ['id' => $user->id]) }}"> {{ $user->name ?: $user->email }}</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {!! $users->render('pagination::bootstrap-4') !!}
@endsection
