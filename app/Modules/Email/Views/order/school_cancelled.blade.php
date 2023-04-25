@component('mail::message', ($course) ? ['email' => $course->school->booking_email] : [])

# Hej {{ $course ? $course->school->name : (isset($order->school) ? $order->school->name : '') }}!

<span style="color:red">En avbokning på din kurs har skett.</span>

## Ordersammanställning
@foreach($order->items as $item)
  <span style="color:red">
    {{ $item->name }} @if($item->isCourse()) {{ $course->start_time->formatLocalized('%d %B kl.%H:%M') }} @endif ×{{ $item->quantity }} **{{ $item->amount }} kr**
  </span>
  @if($item->isCourse())
  <br>{{ trans('courses.' . strtolower($item->participant->type)) }}: {{ $item->participant->name }}
  <br>*Personnummer: {{ $item->participant->social_security_number }}*
  @endif

@endforeach

@component('mail::button', ['url' => route('organization::courses.show', ['id' => $course->id])])
  Visa kurs
@endcomponent

@endcomponent
