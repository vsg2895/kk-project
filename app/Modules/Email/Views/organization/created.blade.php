@component('mail::message')

# Ny organisation {{ $organization->name }}
Org.nr. {{ $organization->org_number }}

{{ $user->given_name }} {{ $user->family_name }}
{{ $user->phone_number }}
{{ $user->email }}

@component('mail::button', ['url' => route('admin::organizations.show', ['id' => $organization->id])])
  Organisationens sida
@endcomponent

@endcomponent
