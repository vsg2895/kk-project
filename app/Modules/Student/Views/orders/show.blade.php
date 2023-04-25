<?php
/** @var \Jakten\Models\Order $order */
?>

@extends('student::layouts.default')
@section('content')
    <header class="section-header student-section">
        @if(!$order->isGiftCardOrder())
            <h1 class="page-title">Beställning hos <a href="{{ route('shared::schools.show', ['citySlug' => $order->school->city->slug, 'slug' => $order->school->slug]) }}">{{ $order->school->name }}</a></h1>
        @else
            <h1 class="page-title">Beställning hos Körkortsjakten, {{ $order->title }}</h1>
        @endif
        <p class="tagline text-muted">Beställningsnummer #{{ $order->id }}</p>
    </header>

    @include('shared::components.message')
    @php($coursesPresent = false)
    @php($packagePresent = false)
    <div class="card card-block mx-0">
        <div class="list">
            @foreach($order->items as $item)
                <div class="list-item">
                    <h3>{{ $item->isGiftCard()? 'Presentkort' : $item->name }} @if($item->isCourse()) @if($item->course->part) {{ $item->course->part }} @endif  @if(!$item->course->part){{ $item->course->start_time->toDateString() }} {{ $item->course->start_hour }}-{{ $item->course->end_hour }} @endif @endif <small class="text-numerical text-muted">{{ $item->amount }} kr</small></h3>
                    <p>
                        @if($item->isCourse())
                            @php($coursesPresent = true)
                            <p>Deltagare: <strong>{{ $item->participant->name }} - {{ $item->participant->social_security_number }} - {{ $item->participant->email }}</strong></p>
                        @elseif(!$item->isGiftCard())
                            @php($packagePresent = true)
                        Antal: <strong>{{ $item->quantity }}</strong>
                        @endif
                    </p>
                    @if($item->isCourse())
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('student::bookings.show', ['id' => $item->id]) }}">Visa kurs</a>
                    @endif
                </div>
            @endforeach
        </div>

        @if($order->canBeCancelled())
            @if($packagePresent)
                <p>Avbokning och ångerrätt gäller i 14 dagar från köptillfället. Det gäller endast avbokningar av paket.</p>
            @endif
            @if($coursesPresent)
                <p>Om du önskar omboka din kurs till ett nytt datum ska detta ske senast 48 h innan kursens starttid. Då får du ett saldo på hela beloppet inklusive bokningsavgiften.</p>
                <form method="POST" action="{{ route('student::orders.rebook', ['id' => $order->id]) }}">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-outline-warning" data-confirm="@lang('form.confirm_action')">Omboka</button>
                </form>
                <br/>
            @endif
            @if($coursesPresent)
                <p>Om du önskar avboka en din kurs ska detta ske senast 48 h innan kursens starttid. Dock är ej bokningsavgiften återbetalningsbar. </p>
            @endif
            <form method="POST" action="{{ route('student::orders.cancel', ['id' => $order->id]) }}">
                {{ csrf_field() }}
                <button type="submit" class="btn btn-outline-danger" data-confirm="@lang('form.confirm_action')">Avboka</button>
            </form>
        @endif
    </div>
@endsection
