@extends('shared::layouts.default')
@section('body-class')
    page-auth
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                @if(Session::has('status'))
                    <h3>{{ Session::get('status') }}</h3>
                @else
                    @include('shared::components.errors')
                    <h3>Återställ lösenord</h3>
                    <form method="POST" action="{{ route('auth::password.reset') }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label class="label-hidden" for="email">E-post</label>
                            <span class="fa fa-envelope form-control-icon"></span>
                            <input id="email" name="email" placeholder="Email" type="email" class="form-control form-control-lg">
                        </div>
                        <button class="btn btn-block btn-black" type="submit">Skicka återställningslänk</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
