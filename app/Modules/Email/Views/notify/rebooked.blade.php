@component('mail::message')

Här kommer en ombokningsbekräftelse på bokning:

{{ __('notify.order_id', ['id' => $order->id]) }}

@endcomponent
