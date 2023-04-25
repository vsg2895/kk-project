@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <nav class="d-flex">
            <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('admin::users.index') !!}">
                @icon('arrow-left')
                <span>Tillbaka</span>
            </a>
            </nav>
        <h1 class="page-title">{{ $user->name }} @if($user->deleted_at)
                <small class="text-danger">Användare borttagen</small>@endif</h1>
    </header>

    @include('shared::components.message')
    @include('shared::components.errors')

    <div class="card card-block mx-0">
        <form method="POST" action="{{ route('admin::users.update', ['id' => $user->id]) }}">
            {{ csrf_field() }}
            <input class="form-control" type="hidden" name="role_id" id="role_id"
                   value="{{ old('role_id', $user->role_id) }}"/>
            <div class="form-group">
                <label for="role_label">Användartyp</label>
                @foreach($roles as $label => $value)
                    @if ($user->role_id === $value)
                        <input class="form-control" type="text" name="role_label" id="role_label"
                               value="{{ trans('roles.'.$label) }}" disabled/>
                    @endif
                @endforeach
            </div>

            <div class="form-group" @if (!$user->isOrganizationUser())style="display: none;" @endif>
                <label for="organization_id">Organisation</label>
                <select class="custom-select" name="organization_id" id="organization_id">
                    @foreach($organizations as $organization)
                        <option value="{{ $organization->id }}"
                                @if($user->organization_id === $organization->id) selected @endif>{{ $organization->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group @if($errors->has('email')) has-error @endif">
                <label for="email">E-post</label>
                <input class="form-control" type="email" name="email" id="email"
                       value="{{ old('email', $user->email) }}"/>
            </div>

            @include('shared::components.user.edit')

            @if($user->role_id === 1)
                <div class="mb-2">
                    <span class="h3">Presentkort</span>
                    @if($user->giftCards->count())
                        <div class="table-head table-row hidden-sm-down">
                            <div class="table-cell col-md-6">
                                Belopp
                            </div>
                            <div class="table-cell col-md-6">
                                Giltighetstid
                            </div>
                        </div>
                        <div class="table">
                            @foreach($user->giftCards as $giftCard)
                                <div class="table-row">
                                    <div class="table-cell col-md-6">
                                        <span class="link hidden-md-up">Belopp : </span>
                                        {{ $giftCard->remaining_balance }} kr
                                    </div>
                                    <div class="table-cell col-md-6">
                                        <span class="link hidden-md-up">Giltighetstid :</span>
                                        {{ Carbon\Carbon::parse($giftCard->expires)->toDateString() }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div>
                            <span class="h4">Total belopp:</span>
                            <span class="h4 text-success">{{ $user->giftCardBalance }} kr</span>
                        </div>
                    @else
                        <no-results title="Eleven har inga presentkort kopplade.">
                        </no-results>
                    @endif
                </div>
            @endif

            @if(auth()->user()->id === $user->id)
                @include('shared::components.user.password')
            @endif

            @if(!$user->deleted_at)
                <button type="submit" class="btn btn-success">Spara</button>
            @endif
        </form>
        @if(!$user->deleted_at)
            <form method="POST" action="{{ route('admin::users.delete', ['id' => $user->id]) }}">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}

                <button type="submit" class="btn btn-outline-danger mt-1" data-confirm="@lang('form.confirm_action')">Ta
                    bort
                </button>
            </form>
        @endif

        @if($user->deleted_at)
            <form method="POST" action="{{ route('admin::users.restore', ['id' => $user->id]) }}">
                {{ csrf_field() }}
                <button type="submit" class="btn btn-outline-primary mt-1">Återskapa användare</button>
            </form>
        @endif

    </div>
@endsection
