@extends('shared::layouts.default')
@section('body-class')
    page-auth
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                <h2>Välj lösenord</h2>

                @include('shared::components.message')
                @include('shared::components.errors')

                <form id="login-form" method="POST" action="{{ route('auth::password.store') }}">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label class="label-hidden" for="email">Lösenord</label>
                        <input class="form-control form-control-lg" placeholder="Lösenord" type="password" id="password" name="password" />
                    </div>

                    <div class="form-group">
                        <label class="label-hidden" for="email">Bekäfta lösenord</label>
                        <input class="form-control form-control-lg" placeholder="Bekäfta lösenord" type="password" id="password_confirmation" name="password_confirmation" />
                    </div>

                    <button class="btn btn-block btn-primary" type="submit">Logga in</button>
                </form>
            </div>
        </div>
    </div>
@endsection
