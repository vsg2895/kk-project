@component('mail::message', ['email' => $order->user->email])

{{ $order->user->getNameAttribute() }}

Tyvärr kunde inte din bokning genomföras. Detta beror på att kursen du försökte boka precis fick slut på platser. Självklart kommer inga pengar att dras från dig.

PaymentId: {{ $order->klarnaPaymentId }}

@endcomponent