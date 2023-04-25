@extends('shared::layouts.dashboard')

{{-- TODO: why isnt Request::is('statistics') working?? --}}

@section('sidebar-nav')
    <a class="nav-item nav-link {{ Request::is('organization') ? 'active' : '' }}" href="{{ route('organization::dashboard') }}">@icon('home') <span>Översikt</span></a>
    @if($schoolCount)
        <a class="nav-item nav-link {{ Request::is('organization/courses*') ? 'active' : '' }}" href="{{ route('organization::courses.index') }}?calendar=true">@icon('course') <span>Kurser</span></a>
    @endif
    <a class="nav-item nav-link {{ Request::is('organization/orders*') ? 'active' : '' }}" href="{{ route('organization::orders.index') }}">@icon('order') <span>Beställningar</span> @if(isset($unhandledOrders) && $unhandledOrders)<span class="tag tag-pill tag-primary">{{ $unhandledOrders }}</span>@endif</a>
    <a class="nav-item nav-link {{ Request::is('organization/ratings*') ? 'active' : '' }}" href="{{ route('organization::ratings.index') }}">@icon('rating') <span>Betyg</span></a>
    <a class="nav-item nav-link {{ Request::is('organization/statistics') ? 'active' : '' }}" href="{{ route('organization::statistics.index') }}">@icon('statistics') <span>Statistik</span></a>
    @if(auth()->user()->organization->schools->count() > 1)
    <a class="nav-item nav-link {{ Request::is('organization/schools*') ? 'active' : '' }}" href="{{ route('organization::schools.index') }}">@icon('school') <span>Trafikskolor</span></a>
    @elseif(auth()->user()->organization->schools->first())
        <a class="nav-item nav-link {{ Request::is('organization/schools/*') ? 'active' : '' }}" href="{{ route('organization::schools.show', ['id' => auth()->user()->organization->schools->first()->id]) }}">@icon('school') <span>Om trafikskolan</span></a>
    @else
        <a class="nav-item nav-link {{ Request::is('organization/schools/create') ? 'active' : '' }}" href="{{ route('organization::schools.create') }}">@icon('school') <span>Om trafikskolan</span></a>
    @endif
    <a class="nav-item nav-link {{ Request::is('organization/information') ? 'active' : '' }}" href="{{ route('organization::organization.show') }}">@icon('organization') <span>Organisation</span></a>
    <a class="nav-item nav-link {{ Request::is('organization/users') ? 'active' : '' }}" href="{{ route('organization::users.index') }}">@icon('user') <span>Användare</span></a>
    <a class="nav-item nav-link {{ Request::is('organization/profile') ? 'active' : '' }}" href="{{ route('organization::user.show') }}">@icon('user') <span>Användarprofil</span></a>
    <a class="nav-item nav-link {{ Request::is('organization/notify') ? 'active' : '' }}" href="{{ route('organization::notify.index') }}">@icon('settings') <span>Notiser</span></a>
@endsection
