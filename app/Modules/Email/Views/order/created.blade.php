@component('mail::message', ['email' => $order->user->email])

# Hej!

@if($isReminder)
Hej här kommer en påminnelse om din bokade kurs.
@else
Här kommer en bekräftelse på din bokning med beställningsnummer {{ $order->id }}.
@endif

---
@foreach($courses as $course)
{{ $course->confirmation_text }}<br>
{{ $course->description }}<br>
{{ $course->address_description }}
---
<br>
@endforeach

@if($toEmail === $order->user->email)
**Totalt: {{ $order->orderValue + $order->giftCardBalanceUsed() }}kr**
@endif

##Information
@foreach($order->items as $item)
@if($toEmail === $item->participant->email || $toEmail === $order->user->email)
@if($item->isGiftCard() && $order->isGiftCardOrder())
{{ $item->quantity }} × Presentkort - {{ $item->amount }} kr (värt {{ $item->giftCard->remaining_balance }})<br>
@elseif(!$item->isGiftCard())
**{{ $item->name }}: ×{{ $item->quantity }} @if($item->isCourse() && !$item->course->part){{ $item->course->start_time->toDateString() }} {{ $item->course->start_hour }}-{{ $item->course->end_hour }}**@endif<br>
@if($item->isCourse())
@if($course->id != 76518){{ $item->school->name }}@endif @if($item->course->part) <br> {{ $item->course->part }}@endif<br>
@if($course->id == 76518)
    {{ $item->course->address }}
@else
    @if($item->course->part) {{ $item->course->address }} @else {{ $item->course->course_address }} @endif
@endif
<br>
<br>
@endif
@if($item->isCourse())
Elev / Handledare: {{ $item->participant->name }}<br>
*Personnummer: {{ $item->participant->social_security_number }}*<br>
*Email: {{ $item->participant->email }}*<br>
@if($item->participant->transmission)
    *Växellåda: {{ $item->participant->transmission }}*<br>
@endif
@if($item->participant->category)
    <b>*MC typ: {{ $item->participant->category }}*</b><br>
@endif
@endif
@endif
{{ $item->amount }} kr<br>
@endif

@endforeach

@if (!$order->isGiftCardOrder() && !$course)
@if ($order->isPackageOrder()) Ta kontakt med trafikskolan för att boka in dina tillfällen. @endif Lycka till med övningskörningen!
@endif

@component('mail::button', ['url' => route('student::orders.show', ['id' => $order->id])])
Visa beställning
@endcomponent

@if($order->isGiftCardOrder())
Viktig information till dig som köpt ett presentkort 

Koden ovan är en värdekod som ska aktiveras på körkortsjakten för att erhålla saldot. För att aktivera värdekoden ska den som avser nyttja presentkortet skapa ett elevkonto på Körkortsjakten. 

Observera att köp från presentkort endast kan ske via körkortsjakten.se

Klipp ut nedan om du vill ge bort ditt presentkort

---

**PRESENTKORT** 

Från:

_ _ _ _ _ _ _ _ _ _ _ _ _ _ _


Till:

_ _ _ _ _ _ _ _ _ _ _ _ _ _ _


Summa: **{{ $order->items->first()->giftCard->remaining_balance }}**

Presentkortskoden: **{{ $order->items->first()->giftCard->token }}**

Dagens datum: **{{ Carbon\Carbon::now()->toDateString() }}**


Så här gör du:
 
Koden ovan är en presentkortskod som ska aktiveras på körkortsjakten för att erhålla saldot. För att aktivera presentkortskoden ska du som avser nyttja presentkortet skapa ett elevkonto på Körkortsjakten. 

Aktivera din presentkortskod på ditt elevkonto. Efter det är du redo att boka och betala kurser. Ditt saldo fungerar som betalmedel på alla trafikskolor som är anslutna till körkortsjaktens presentkort och har en symbol med ett paket vid trafikskolans namn. Vid betalning anger du om du vill betala med ditt presentkort, Klarna eller både och. 

Observera att köp från ditt presentkort endast kan ske via körkortsjakten.se 

Presentkortets giltighetstid är 12 månader från köptillfället. Presentkortet måste användas inom giltighetstiden. 

Vid frågor kontakta kontakta Körkortsjakten på kontakt@korkortsjakten.se
@endif

@endcomponent
