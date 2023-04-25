@component('mail::message')

# Hej {{ $user->given_name }}

__Vi har fått en begäran om att återställa ditt lösenord.__

@component('mail::button', ['url' => route('auth::password.reset.do', ['email' => $user->email, 'token' => $token])])
  Återställ lösenord
@endcomponent

Om detta inte var du kan du ignorera detta email.

@endcomponent
