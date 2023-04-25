@extends('student::layouts.default')
@section('content')
    <header class="section-header student-section">
        <h1 class="page-title">{{ $booking->course->name }} hos <a href="{{ route('shared::schools.show', ['citySlug' => $booking->course->school->city->slug, 'slug' => $booking->course->school->slug]) }}">{{ $booking->course->school->name }}</a></h1>
        <h3 class="text-primary">{{ $booking->course->start_time->formatLocalized('%H:%M, %A %d:e %B, %Y') }}</h3>
    </header>

    <div class="card card-block mx-0">

        <h4>Deltagare</h4>
        <p>{{ $booking->participant->name }}<br>
        Personnummer: {{ $booking->participant->social_security_number }}</p>

        <h4>Adress</h4>
        <p>
            {{ $booking->course->address }}<br>
            {{ $booking->course->zip }} {{ $booking->course->postal_city }}
        </p>
        <h4>Kontakt</h4>
        <p>
            <a href="tel:{{ $booking->course->school->phone_number }}">{{ $booking->course->school->phone_number }}</a><br>
            <a href="mailto:{{ $booking->course->school->contact_email }}">{{ $booking->course->school->contact_email }}</a>
        </p>
        <h4>Anteckningar</h4>
        <p>{{ $booking->course->address_description }}</p>
        <p>{{ $booking->course->description }}</p>
    </div>
@endsection
