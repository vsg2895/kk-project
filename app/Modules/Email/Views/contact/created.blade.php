@component('mail::message', ['email' => $contactRequest->email])

## {{ $contactRequest->title }}

@if($contactRequest->school)
  För trafikskola: **{{ $contactRequest->school->name }}**
@endif

från *{{ $contactRequest->name }}* [{{ $contactRequest->email }}](mailto:{{ $contactRequest->email }})

---

{{ $contactRequest->message }}

---

@endcomponent
