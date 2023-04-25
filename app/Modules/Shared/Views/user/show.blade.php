@extends($type . '::layouts.default')

@section('content')
    <header class="section-header">
        <h1 class="page-title">{{ $user->name }}</h1>
    </header>

    @include('shared::components.errors')
    @include('shared::components.message')

    <div class="col-lg-8 col-xl-4 px-0">
        <div class="card card-block mx-0">
            <form method="POST" action="{{ Request::url() }}">
                {{ csrf_field() }}

                @include('shared::components.user.edit')
                @if(auth()->user()->isAdmin() || auth()->user()->id === $user->id)
                    @include('shared::components.user.password')
                @endif

                <button class="btn btn-success" type="submit">Spara</button>
            </form>
        </div>

        <div class="card card-block bg-danger-light mx-0">
            <p class="text-danger">
                <strong>Radera @if(auth()->user()->id === $user->id) mitt @endif konto.</strong>
                @if($user->isStudent())
                    <br>Detta har ingen effekt på redan bekräftade beställningar.
                @endif
            </p>

            <form method="POST" action="{{ route('student::user.delete') }}">
                {{ csrf_field() }}
                <button class="btn btn-danger" type="submit" data-confirm="@lang('form.confirm_action')" value="deactivate">Radera nu</button>
            </form>
        </div>
    </div>
@endsection
