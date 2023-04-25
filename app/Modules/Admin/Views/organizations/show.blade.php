@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('admin::organizations.index') !!}">@icon('arrow-left') Tillbaka</a>
        <h1 class="page-title">{{ $organization->name }} @if($organization->deleted_at)<small class="text-danger">Organisationen är borttagen</small> @endif</h1>

    </header>

    @include('shared::components.message')
    @include('shared::components.errors')

    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#tab-info" role="tab">Organisation</a>
        </li>

        @if(count($organization->comments))
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tab-annotations" role="tab">
                    Systemmeddelanden <span class="tag tag-sm tag-default tag-pill">{{ count($organization->comments) }}</span>
                </a>
            </li>
        @endif
    </ul>

    <div class="card card-block">
        <div class="tab-content">
            <div class="tab-pane active" id="tab-info" role="tabpanel">
                <p class="text-muted">Klarna status: {{ trans('klarna.status.' . $organization->sign_up_status) . $organization->sign_up_rejected_reason }}</p>
                @if($organization->payment_id)
                    <p class="text-muted">Klarna ID: {{ $organization->payment_id }}</p>
                    <p class="text-muted">Klarna Secret: {{ $organization->payment_secret }}</p>
                @endif
                <form method="POST" action="{{ route('admin::organizations.update', ['id' => $organization->id]) }}" enctype="multipart/form-data">
                    @include('shared::components.organization.edit')

                    @if(!$organization->deleted_at)
                        <button type="submit" class="btn btn-sm btn-success">Spara</button>
                    @endif
                </form>
                @if(!$organization->deleted_at)
                    <form method="POST" action="{{ route('admin::organizations.delete', ['id' => $organization->id]) }}">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}

                        <button type="submit" class="btn btn-sm btn-outline-danger mt-1" data-confirm="@lang('form.confirm_action')">Ta bort organisation</button>
                    </form>

                    <br><br>

                    <div class="clearfix">
                        <h3 class="float-sm-left">Användare</h3>
                        <a class="btn btn-sm btn-outline-primary float-sm-right" href="{{ route('admin::users.create', ['organization' => $organization->id]) }}">Lägg till användare @icon('plus')</a>
                    </div>
                    <div class="table">
                        <div class="table-head table-row">
                            <div class="table-cell">
                                Namn
                            </div>
                       </div>
                        @foreach($organization->users as $user)
                            <div class="table-row">
                                <div class="table-cell">
                                    <a href="{{ route('admin::users.show', ['id' => $user->id]) }}">{{ $user->name }}</a>
                                </div>
                           </div>
                        @endforeach
                    </div>

                    <br><br>

                    <div class="clearfix">
                        <h3 class="float-sm-left">Trafikskolor</h3>
                        <a class="btn btn-sm btn-outline-primary float-sm-right" href="{{ route('admin::schools.create', ['organization' => $organization->id]) }}">Lägg till skola @icon('plus')</a>
                    </div>
                    <div class="table">
                        <div class="table-head table-row">
                            <div class="table-cell col-xs-8">
                                Namn
                            </div>
                            <div class="table-cell col-xs-4">
                                Stad
                            </div>
                       </div>
                        @foreach($organization->schools as $school)
                            <div class="table-row">
                                <div class="table-cell col-xs-8">
                                    <a href="{{ route('admin::schools.show', ['id' => $school->id]) }}">{{ $school->name }}</a>
                                </div>
                                <div class="table-cell col-xs-4">
                                    {{ $school->city->name }}
                                </div>
                           </div>
                        @endforeach
                    </div>
                </div>
            @endif
            <div class="tab-pane" id="tab-annotations" role="tabpanel">
                @include('admin::components.annotation.list', ['list' => $organization->comments])
            </div>
        </div>       
    </div>
@endsection
