@extends('shared::layouts.default')
@section('pageTitle', 'Skapa elevkonto -')
@section('body-class', 'page-auth')
@section('content')
    <div id="login-content" class="container">
        <div class="row">
            <div class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                <h1>Skapa konto</h1>

                <div>
                    <form id="login-form" method="POST" action="{{ route('auth::register.student.store') }}">
                        {{ csrf_field() }}

                        <div class="form-group @if($errors->has('given_name')) has-error @endif">
                            <label class="label-hidden" for="given_name">Förnamn</label>
                            <span class="far fa-user form-control-icon"></span>
                            <input class="form-control @if($errors->has('given_name')) form-control-error @endif form-control-lg" placeholder="Förnamn" type="text" id="given_name" name="given_name" value="{{ old('given_name') }}" />
                            @if($errors->has('given_name'))
                                <div class="invalid-feedback">{{ $errors->first('given_name') }}</div>
                            @endif
                        </div>

                        <div class="form-group @if($errors->has('family_name')) has-error @endif">
                            <label class="label-hidden" for="family_name">Efternamn</label>
                            <span class="far fa-user form-control-icon"></span>
                            <input class="form-control @if($errors->has('family_name')) form-control-error @endif form-control-lg" placeholder="Efternamn" type="text" id="family_name" name="family_name" value="{{ old('family_name') }}" />
                            @if($errors->has('family_name'))
                                <div class="invalid-feedback">{{ $errors->first('family_name') }}</div>
                            @endif
                        </div>

                        <div class="form-group @if($errors->has('email')) has-error @endif">
                            <label class="label-hidden" for="email">E-post</label>
                            <span class="far  fa-envelope form-control-icon"></span>
                            <input class="form-control @if($errors->has('email')) form-control-error @endif form-control-lg" placeholder="Email" type="email" id="email" name="email" value="{{ old('email') }}" />
                            @if($errors->has('email'))
                                <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                            @endif
                        </div>

                        <div class="form-group @if($errors->has('phone_number')) has-error @endif">
                            <label class="label-hidden" for="phone_number">Telefonnummer</label>
                            <span class="fa fa-phone form-control-icon"></span>
                            <input class="form-control @if($errors->has('phone_number')) form-control-error @endif form-control-lg" placeholder="Telefonnummer" type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" />
                            @if($errors->has('phone_number'))
                                <div class="invalid-feedback">{{ $errors->first('phone_number') }}</div>
                            @endif
                        </div>

                        <div class="form-check">
                            <label class="form-checkbox-wrapper">
                                <input type="checkbox" id="terms" name="terms" required>
                                <span class="checkmark"></span>
                                <div class="label-text">
                                    Genom att skapa ett konto accepterar du Körkortsjaktens <a href="{{ url('/villkor') }}">användarvillkor</a>.
                                </div>
                            </label>
                            @if($errors->has('terms'))
                                <div class="invalid-feedback">{{ $errors->first('terms') }}</div>
                            @endif
                        </div>

                        <button class="btn btn-black" type="submit">Skapa konto</button>
                    </form>
                </div>

                <p><span class="lead">Har du en trafikskola?</span><br><a href="{{ route('auth::register.organization') }}">Registrerar dig <i>här</i> istället</a></p>
            </div>
        </div>
    </div>
    <div class="license-front"></div>
@endsection
