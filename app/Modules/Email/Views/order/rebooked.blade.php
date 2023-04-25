@component('mail::message', ['email' => $order->user->email])

# Hej {{ $order->user->given_name }}!

Här kommer en ombokningsbekräftelse på din bokning. Ditt saldo har nu uppdaterats på ditt konto på Körkortsjakten, och du kan nu boka ett nytt tillfälle med ditt saldo på valfri skola. När du kommer till kassan appliceras ditt saldo automatiskt, viktigt är att du är inloggad.

@foreach($order->items as $item)
  {{ $item->name }}: ×{{ $item->quantity }}
  **{{ $item->amount }} kr <span style="color: red;">Avbokad</span>**

@endforeach

@component('mail::button', ['url' => route('student::orders.show', ['id' => $order->id])])
  Visa beställning
@endcomponent

@endcomponent
