@extends('shared::layouts.default')
@section('pageTitle')
    Trafikskolor | {{ $city->name }} |
@stop
    @section('content')
    <h3>Trafikskolor i {{ $city->name }}</h3>

    <ul>
        @foreach($schools as $school)
            <li>
                <a href="{{ route('shared::schools.show', ['city' => $school->city->slug, 'school' => $school->slug]) }}">
                    {{ $school->name }}
                </a>
            </li>
        @endforeach
    </ul>
@endsection
