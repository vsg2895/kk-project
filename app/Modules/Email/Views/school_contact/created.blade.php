@component('mail::message', ['email' => $contactRequest->email])

## {{ $contactRequest->title }}
från *{{ $contactRequest->name }}* [{{ $contactRequest->email }}](mailto:{{ $contactRequest->email }})

---

{{ $contactRequest->message }}

---

@endcomponent
