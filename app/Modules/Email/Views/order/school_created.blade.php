@component('mail::message', ($course) ? ['email' => $course->school->booking_email] : [])

# Hej @if($order->school){{ $order->school->name }} @endif!

@if($course)
En bokning har skett på din Trafikskola
@endif

## Bokningsdetaljer
Beställningsnummer : *{{ $order->id }}*<br>
Namn: *{{ $order->user->name }}*<br>
Email: *{{ $order->user->email }}*<br>
Telefonnummer: *{{ $order->user->phone_number }}*<br>

## Ordersammanställning
@if($order->school && $order->school->hasKlarna)
{{ $order->orderValue }}kr Klarna
@endif

@if($order->giftCardBalanceUsed())
{{ $order->giftCardBalanceUsed() }}kr Körkortsjakten
@endif

**Totalt: {{ $order->orderValue + $order->giftCardBalanceUsed() }}kr**

##Information
@foreach($order->items as $item)
@if($item->isGiftCard() && $order->isGiftCardOrder())
{{ $item->quantity }} × Presentkort - {{ $item->amount }} kr (värt {{ $item->giftCard->remaining_balance }})<br>
@elseif(!$item->isGiftCard())
**{{ $item->name }}: ×{{ $item->quantity }} @if($item->isCourse() && !$item->course->part){{ $item->course->start_time->toDateString() }} {{ $item->course->start_hour }}-{{ $item->course->end_hour }}**@endif<br>
@if($item->isCourse())
@if($item->course->part) {{ $item->course->part }} <br> @endif
{{ $item->school->name }}<br>
@if(!$item->course->part) {{ $item->course->course_address }} @else {{ $item->course->address }} @endif<br>
@endif
@if($item->isCourse())
{{ trans('courses.' . strtolower($item->participant->type)) }}: {{ $item->participant->name }}<br>
*Personnummer: {{ $item->participant->social_security_number }}*<br>
*Email: {{ $item->participant->email }}*<br>
*Telefonnummer: {{ $item->participant->phone_number }}*<br>
@if($item->participant->transmission)
    *Växellåda: {{ $item->participant->transmission }}*<br>
@endif
@if($item->participant->category)
    <b>*MC typ: {{ $item->participant->category }}*</b><br>
@endif
@else
@if($item->packageParticipant)
{{ trans('courses.' . strtolower($item->packageParticipant->type)) }}: {{ $item->packageParticipant->name }}<br>
*Personnummer: {{ $item->packageParticipant->social_security_number }}*<br>
*Email: {{ $item->packageParticipant->email }}*<br>
@endif
@endif
@endif
{{ $item->amount }} kr<br>

@endforeach

@if($course)
  @component('mail::button', ['url' => route('organization::courses.show', ['id' => $course->id])])
    Visa kurs
  @endcomponent
@endif

@endcomponent
