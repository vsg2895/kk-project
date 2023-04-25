@extends('organization::layouts.default')
@section('content')
    <header class="section-header">
        <h1 class="page-title">Beställningar</h1>
    </header>

    <div class="card card-block">
        @if($orders->count())
            <div class="table">
                <div class="table-head table-row">
                    <div class="table-cell col-md-1">
                        #
                    </div>
                    <div class="table-cell col-md-3">
                        Kurs
                    </div>
                    <div class="table-cell col-md-2">
                        Beställare
                    </div>
                    <div class="table-cell col-md-2">
                        Beställningsdatum
                    </div>
                    <div class="table-cell col-md-2">
                        Status
                    </div>
                    <div class="table-cell col-md-2">
                        Ordervärde
                    </div>
                </div>
                @foreach($orders as $order)
                    <div class="table-row">
                        <div class="table-cell col-md-1">
                            <a href="{{ route('organization::orders.show', ['id' => $order->id]) }}">
                                #{{ $order->id }}
                            </a>
                        </div>
                        <div class="table-cell col-md-3" style="word-break: break-word">
                            @if($order->items->where('course_id', '!=', null)->count())
                                <strong><a href="{{ route('organization::schools.show', ['id' => $order->items->where('course_id', '!=', null)->first()->school->id ]) }}">
                                        {{ $order->items->where('course_id', '!=', null)->first()->school->name }}</a></strong>
                                {{ $order->getFirstCourse() }}
                                {{ $order->items->where('course_id', '!=', null)->first()->course->start_time->formatLocalized('%d:e %b, %H:%M') }}
                            @else
                                Ej kursbeställning
                            @endif
                        </div>
                        <div class="table-cell col-md-2">
                            {{ $order->user_email_or_name ?? 'ta bort användare' }}
                        </div>
                        <div class="table-cell col-md-2">
                            {{ $order->created_at->formatLocalized('%Y-%m-%d, %H:%M') }}
                        </div>
                        <div class="table-cell col-md-2" style="word-break : break-word;">
                            <a href="{{ route('organization::orders.show', ['id' => $order->id]) }}">
                                @if($order->cancelled)
                                    <span class="text-danger">Beställningen har avbokats</span>
                                @else
                                    @if(!$order->cancelled && $order->handled)
                                        <span class="text-success">Beställningen har hanterats</span>
                                    @else
                                        <span class="text-warning">Artiklar kvar att hantera</span>
                                    @endif
                                @endif
                            </a>
                        </div>
                        <div class="table-cell col-md-2">
                            {{ $order->school_order_value + $order->giftCardBalanceUsed() }} SEK
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <no-results title="Inga beställningar hittades"></no-results>
        @endif

        <p>Har du frågor kring utbetalningar, orderhanteringar eller faktura Klarna? Här får du svar på de vanligaste
            frågorna.</p>
        <a class="btn btn-primary" target="_blank" href="https://help.klarna.com/sv/k%C3%B6rkortsjakten">Klarna FAQ
            →</a>
    </div>

    {!! $orders->render('pagination::bootstrap-4') !!}

@endsection
