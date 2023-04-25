@component('mail::message')

# Hej {{ $user->given_name }}

__Ditt konto har skapats__

@component('mail::button', ['url' => route('auth::confirm', ['email' => $user->email, 'token' => $token->token])])
  Bekräfta email
@endcomponent

@endcomponent
