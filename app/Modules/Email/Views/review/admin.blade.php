@component('mail::message', ['email' => $user->email])

# Hej!

{{ $event->user->getNameAttribute() }} Lämnade ett omdöme på {{ $event->school->name }} efter sin kurs {{ trans('vehicle_segments.' . strtolower($event->course->segment->name)) }}.
För att godkänna kommentaren gå till skolans <a href="{{ route('admin::ratings.edit', $event->rating->id) }}">sida</a>

@endcomponent
