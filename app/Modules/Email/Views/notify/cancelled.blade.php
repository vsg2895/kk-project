@component('mail::message', ['email' => $order->user->email])

Här kommer en avbokningsbekräftelse på din bokning.

{{ __('notify.order_id', ['id' => $order->id]) }}

@endcomponent
