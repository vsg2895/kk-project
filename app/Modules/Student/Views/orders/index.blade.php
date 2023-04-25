@extends('student::layouts.default')
@section('content')
    @include('shared::components.message')
    <header class="section-header student-section">
        <h1 class="page-title">Beställningar</h1>
    </header>


    <div class="card card-block mx-0">
        @if($orders->count())
            <div class="table">
                <div class="table-head table-row hidden-sm-down">
                    <div class="table-cell col-md-3">Kurs</div>
                    <div class="table-cell col-md-3">Startdatum</div>
                    <div class="table-cell col-md-2 text-md-center">Beställningsnr.</div>
                    <div class="table-cell col-md-2 text-md-right">Summa</div>
                    <div class="table-cell col-md-2 text-md-right"></div>
                </div>
                @foreach($orders as $order)
                    <div class="table-row">
                        <div class="table-cell col-md-3">
                            <a href="{{ route('student::orders.show', ['id' => $order->id]) }}">
                                @foreach($order->items as $item)
                                    @if (strpos($item->type, '_') !== false)
                                        {{ trans('vehicle_segments.' . strtolower($item->type)) }}
                                        @if($item->isCourse() && $item->course->part) {{ $item->course->part }} @endif
                                    @else
                                        {{ $item->type }}
                                    @endif
                                    <br/>
                                @endforeach
                            </a>
                        </div>
                        <div class="table-cell col-md-3">
                            @foreach($order->items as $item)
                                @if ($item->isCourse())
                                    {{ $item->course->start_time->formatLocalized('%A %d:e %B, %Y') }}
                                @else
                                    @php($createdAt = new \Carbon\Carbon($order->created_at))
{{--                                    {{ $createdAt->subDays(config('fees.cancel_packages_allowed'))->formatLocalized('%A %d:e %B, %Y') }}--}}
                                    {{ $createdAt->formatLocalized('%A %d:e %B, %Y') }}
                                @endif
                                <br/>
                            @endforeach
                </div>
                <div class="table-cell col-md-2 text-md-center">
                    <span class="hidden-md-up">Beställningsnr. </span><strong>#{{ $order->id }}</strong>
                        </div>
                        <div class="table-cell hidden-md-up more-button">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('student::orders.show', ['id' => $order->id]) }}">Visa</a>
                        </div>
                        <div class="table-cell col-md-2 text-md-right">
                            <span class="text-numerical">
                                {{ $order->cancelled ? 0 : $order->order_value + $order->giftCardBalanceUsed() }} kr
                            </span>
                        </div>
                        <div class="table-cell col-md-2 text-md-right">
                            @if ($order->rebooked)
                                <span class="text-warning">
                                    Ombokad
                                </span>
                            @endif
                            @if ($order->cancelled && !$order->rebooked)
                                <span class="text-danger">
                                    Avbokad
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <no-results title="Du har inte lagt några beställningar ännu">
                <a class="btn btn-primary btn-primary" href="{{ route('shared::introduktionskurs') }}" slot="description">Sök efter lediga kurser</a>
            </no-results>
        @endif
    </div>
@endsection
