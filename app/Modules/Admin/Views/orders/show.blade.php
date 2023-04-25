@extends('admin::layouts.default')
@section('content')

    @include('shared::components.message')

    <header class="section-header">
        <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('admin::orders.index') !!}">@icon('arrow-left') Tillbaka</a>
        <h1 class="page-title">Beställningar
            @if($order->cancelled)<small class="text-danger">Beställningen har avbokats</small>@endif
            @if($order->rebooked)<small class="text-warning">Ombokad</small>@endif
        </h1>
    </header>

    <div class="card card-block">
        <div class="row">
            <div class="col-md-12">
                <p>
                    Namn: {{ $order->user->name }}<br>
                    Telefonnummer: {{ $order->user->phone_number ?: 'Saknas' }}<br>
                    Email: {{ $order->user->email }}<br>
                    Betalningssätt: {{ trans('payment.types.' . $order->payment_method) }} @if($order->includesGiftCard() && !$order->isGiftCardOrder()) & Körkortsjakten @endif<br>
                    @if($order->items->count())
                        @if(!$order->getFirstOrderItem()->isGiftCard())
                            Status: {{ $order->handled ? $order->cancelled ? 'Beställningen har avbokas' : 'Trafikskola har hanterat order' : 'Trafikskolan har inte hanterat ordern än' }}
                        @endif
                    @else
                        Status: <span class="text-danger">Fel (beställningsobjekt hittades inte).</span>
                    @endif
                </p>
                <div class="list">
                    @php($coursesPresent = false)
                    @php($packagePresent = false)
                    @foreach($order->items as $item)
                        <div class="list-item">
                            <h3>{{ $item->name }} @if($item->isCourse()) @if($item->course->part) {{ $item->course->part }} @endif <small>{{ $item->course->start_time->formatLocalized('%A %d:e %B, %Y (%H:%M)') }}</small>@endif</h3>
                            <div><strong>{{ $item->amount }} kr</strong></div>
                            <div>Antal: <strong>{{ $item->quantity }}</strong></div>
                            @if($item->benefit && $item->benefit->benefit_type === \Jakten\Services\StudentLoyaltyProgramService::BENEFIT_TYPES['discount'])
                                Benefit: - {{$item->benefit->amount}} % | {{$item->amount * $item->benefit->amount / 100}} kr
                            @endif

                            @if(!$item->isCourse())
                                @php($coursesPresent = true)
                                @if($item->packageParticipant  && $item->packageParticipant->name && $item->packageParticipant->social_security_number && $item->packageParticipant->email)
                                    <p>Deltagare: <strong>{{ $item->packageParticipant->name }} - {{ $item->packageParticipant->social_security_number }} - {{ $item->packageParticipant->email }} - {{ $item->packageParticipant ? $item->packageParticipant->phone_number : 0}}</strong></p>
                                @endif
                            @endif
                            @if($item->isCourse())
                                @php($coursesPresent = true)
                                @if($item->participant  && $item->participant->name && $item->participant->social_security_number && $item->participant->email)
                                    <p>Deltagare: <strong>{{ $item->participant->name }} - {{ $item->participant->social_security_number }} - {{ $item->participant->email }} - {{ $item->participant ? $item->participant->phone_number : 0 }}</strong></p>
                                @endif
                            @endif
                            @if(!$item->isGiftCard())
                                @if($item->isCourse())
                                    @php($packagePresent = true)
                                @endif
                                <p>Status:
                                    @if ($item->delivered && !$item->credited)
                                        <strong class="text-success">Att fakturera: {{ (($item->amount * $item->quantity) * $item->provision) / 100 }}</strong>
                                    @elseif($item->credited)
                                        <strong class="text-danger">Har krediterats</strong>
                                    @endif
                                    @if($order->cancelled)
                                        <strong class="text-danger">Beställningen har avbokats</strong>
                                    @endif
                                    @if($order->rebooked)
                                        <strong class="text-warning">Ombokad</strong>
                                    @endif
                                </p>
                            @endif
                        </div>
                    @endforeach

{{--                    This lines are out from foreach, no mind how it worked before--}}
                    @if(isset($item) && isset($item->participant) && $item->participant && $item->participant->transmission)
                        <form method="POST" action="{{ route('admin::orders.transmission', ['id' => $order->id]) }}">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <input name="order_id" hidden="hidden" value="{{ $order->id }}">

                                <label>Skola</label>
                                <select name="transmission" class="form-control">
                                        <option value="manual" @if($item->participant->transmission == 'manual') selected @endif >Manual</option>
                                        <option value="automatic" @if($item->participant->transmission == 'automatic') selected @endif >Automatic</option>
                                </select>

                            </div>
                            <button class="btn btn-outline-primary" type="submit">Update Transmission Type</button>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
    @if($order->handled && !$order->cancelled)
        <div class="card card-block">
            <h3>Faktura</h3>
            @if (!$order->invoice_sent)
                <p>Att fakturera: <strong>{{ $order->invoice_amount }} SEK</strong></p>
                <form method="POST" action="{{ route('admin::orders.invoice_sent', ['id' => $order->id]) }}">
                    {{ csrf_field() }}
                    <button class="btn btn-outline-primary" type="submit">Markera faktura skickad @icon('check')</button>
                </form>
            @else
                <p>Fakturerat: {{ $order->invoice_amount }} SEK </p>
            @endif
        </div>
    @endif

    @if($user->isAdmin() && !$order->cancelled && $order->isKlarnaV3())
        <div class="card card-block">
            <h3>@if($order->handled) Återbetalning @else Annullering @endif</h3>
            @if($order->invoice_amount > 0)<p>Att fakturera: <strong>{{ $order->invoice_amount }} SEK</strong></p>@endif
            <form method="POST" action="{{ route('admin::admin::orders.cancel', ['id' => $order->id]) }}">
                {{ csrf_field() }}
                <button class="btn btn-outline-danger" type="submit">Annullering @icon('check')</button>
            </form>

            <br/>
            @if($coursesPresent)
                <form method="POST" action="{{ route('admin::admin::orders.rebook', ['id' => $order->id]) }}">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-outline-warning" data-confirm="@lang('form.confirm_action')">Omboka</button>
                </form>
            @endif

            <br>
            <form method="get" action="{{ route('admin::orders.edit', ['id' => $order->id]) }}">
                <button type="submit" class="btn btn-outline-primary">Ändra</button>
            </form>
        </div>
    @endif
@endsection
<script>
    import Form from "../../../../../resources/assets/js/vue/pages/courses/Form";
    export default {
        components: {Form}
    }
</script>
