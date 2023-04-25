@extends('shared::layouts.dashboard')

@section('sidebar-nav')
    <a class="nav-item nav-link {{ Request::is('admin') ? 'active' : '' }}" href="{{ route('admin::dashboard') }}">@icon('home') <span>Översikt</span></a>
    <a class="nav-item nav-link {{ Request::is('admin/orders*') ? 'active' : '' }}" href="{{ route('admin::orders.index') }}?sort=order_created">@icon('order') <span>Beställningar</span></a>
    <a class="nav-item nav-link {{ Request::is('admin/reports/schools*') ? 'active' : '' }}" href="{{ route('admin::reports.schools', ['start_time' => \Carbon\Carbon::now()->subWeek()->format('Y-m-d'), 'end_time' => \Carbon\Carbon::now()->format('Y-m-d')]) }}">@icon('report') <span>Rapporter</span></a>
    <a class="nav-item nav-link {{ Request::is('admin/courses*') ? 'active' : '' }}" href="{{ route('admin::courses.index') }}">@icon('course') <span>Kurser</span></a>
    <a class="nav-item nav-link {{ Request::is('admin/search-algorithm') ? 'active' : '' }}" href="{{ route('admin::search_algorithm.index') }}">@icon('asterisk') <span>Sökalgoritm</span></a>
    <a class="nav-item nav-link {{ Request::is('admin/ratings*') ? 'active' : '' }}" href="{{ route('admin::ratings.index') }}">@icon('rating') <span>Betyg</span></a>
    <a class="nav-item nav-link {{ Request::is('admin/statistics') ? 'active' : '' }}" href="{{ route('admin::statistics.index') }}">@icon('statistics') <span>Statistik</span></a>
    <a class="nav-item nav-link {{ Request::is('admin/schools*') ? 'active' : '' }}" href="{{ route('admin::schools.index') }}">@icon('school') <span>Trafikskolor</span></a>
    <a class="nav-item nav-link {{ Request::is('admin/organizations*') ? 'active' : '' }}" href="{{ route('admin::organizations.index') }}">@icon('organization') <span>Organisationer</span></a>
    <a class="nav-item nav-link {{ Request::is('admin/cities*') ? 'active' : '' }}" href="{{ route('admin::cities.index') }}">@icon('school') <span>Städer</span></a>
    <a class="nav-item nav-link {{ Request::is('admin/users*') && !Request::is('admin/users/'.auth()->user()->id) ? 'active' : '' }}" href="{{ route('admin::users.index') }}">@icon('users') <span>Användare</span></a>
    <a class="nav-item nav-link {{ Request::is('admin/pages*') ? 'active' : '' }}" href="{{ route('admin::pages.index') }}">@icon('pages-edit') <span>Sidor</span></a>
    <a class="nav-item nav-link {{ Request::is('admin/users/'.auth()->user()->id) ? 'active' : '' }}" href="{{ route('admin::users.show', ['id' => auth()->user()->id]) }}">@icon('user') <span>Användarprofil</span></a>
    <a class="nav-item nav-link {{ Request::is('admin/notify*') ? 'active' : '' }}" href="{{ route('admin::notify.index') }}">@icon('settings') <span>Notiser</span></a>
    <a class="nav-item nav-link {{ Request::is('admin/gift-cards*') ? 'active' : '' }}" href="{{ route('admin::gift_cards.index') }}">@icon('gift-red') <span>Presentkort</span></a>
    <a class="nav-item nav-link {{ Request::is('admin/partners*') ? 'active' : '' }}" href="{{ route('admin::partners.index') }}">@icon('ticket') <span>Partners</span></a>
    <a class="nav-item nav-link {{ Request::is('admin/course_type*') ? 'active' : '' }}" href="{{ route('admin::course_type.index') }}">@icon('car') <span>Course types</span></a>
    <a class="nav-item nav-link {{ Request::is('blog/admin/posts*') ? 'active' : '' }}" href="{{ route('blog::posts.indexAdmin') }}">@icon('pages-edit')<span>Blog</span></a>
@endsection
