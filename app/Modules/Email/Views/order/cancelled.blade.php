@component('mail::message', ['email' => $order->user->email])

# Hej {{ $order->user->given_name }}!

Här kommer en avbokningsbekräftelse på din bokning.

@foreach($order->items as $item)
  {{ $item->name }}: ×{{ $item->quantity }}
  **{{ $item->amount }} kr**

@endforeach

@component('mail::button', ['url' => route('student::orders.show', ['id' => $order->id])])
  Visa beställning
@endcomponent

@endcomponent
