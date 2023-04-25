@extends('shared::layouts.dashboard')

@section('sidebar-nav')
    <a class="nav-item nav-link {{ Request::is('student/bookings*') ? 'active' : '' }}" href="{{ route('student::bookings.index') }}">@icon('course') <span>Mina kurser</span></a>
    <a class="nav-item nav-link {{ Request::is('student/orders*') ? 'active' : '' }}" href="{{ route('student::orders.index') }}">@icon('order') <span>Beställningar</span></a>
    <a class="nav-item nav-link {{ Request::is('student/ratings*') ? 'active' : '' }}" href="{{ route('student::ratings.index') }}">@icon('rating') <span>Betyg</span></a>
    <a class="nav-item nav-link {{ Request::is('student/profile') ? 'active' : '' }}" href="{{ route('student::user.show') }}">@icon('user') <span>Användarprofil</span></a>
    <a class="nav-item nav-link {{ Request::is('student/notify') ? 'active' : '' }}" href="{{ route('student::notify.index') }}">@icon('settings') <span>Notiser</span></a>
    <a class="nav-item nav-link {{ Request::is('student/gift-cards') ? 'active' : '' }}" href="{{ route('student::giftcard.index') }}">@icon('gift-red') <span>Presentkort</span></a>
@endsection
