@extends('shared::layouts.default')

@section('content')
    <h1 class="posts-title text-xs-center">Blogg</h1>

    <posts-page :role-id="{{ Auth::check() ? Auth::user()->role_id : 0 }}"></posts-page>
@endsection
