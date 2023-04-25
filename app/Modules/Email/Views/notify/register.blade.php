@component('mail::message', ['email' => $user->email])

# {{ $user->getNameAttribute() }}

{{ __('notify.registered_at', ['date' => $user->created_at]) }}

@endcomponent