@component('mail::message', ['email' => $user->email])

# Hej {{ $user->given_name }}!

Tyvärr kunde inte din bokning genomföras. Detta beror på att kursen du försökte boka precis fick slut på platser. Självklart kommer inga pengar att dras från dig.

@component('mail::button', ['url' => route('shared::introduktionskurs', ['citySlug' => $course->city->slug])])
Hitta en liknande kurs
@endcomponent

@endcomponent
