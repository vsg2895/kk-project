@component('mail::message', ['email' => $order->user->email])

# Hej {{ $order->user->given_name }}!

Här kommer en bekräftelse på din bokning med beställningsnummer {{ $order->id }}.

---

@foreach($order->items as $item)
  @if($item->isGiftCard())
  {{ $item->quantity }} × Presentkort - {{ $item->amount }} kr (värt {{ $item->giftCard->remaining_balance }})

  Presentkortskod:

  **{{ $item->giftCard->token }}**
  @else
  {{ $item->name }}: ×{{ $item->quantity }}

  @if($item->isCourse() && $item->course->part)<br> {{ $item->course->part }} <br>@endif

  **{{ $item->amount }} kr**
  @endif

@endforeach

@component('mail::button', ['url' => route('student::orders.show', ['id' => $order->id])])
  Visa beställning
@endcomponent

@endcomponent
