@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <h1 class="page-title">Beställningar</h1>
    </header>

    @include('shared::components.message')
    @include('shared::components.info')

    @component('form.search')
        @slot('addons')
            <div class="my-1">
                <label>Med status</label>
                <div class="d-lg-flex">
                    @foreach([['value' => 'handled', 'label' => 'Att fakturera'], ['value' => 'unhandled', 'label' => 'Ej levererad'], ['value' => 'invoice_sent', 'label' => 'Faktura skickad']] as $status)
                        <div class="mr-1">
                            <input id="{{ $status['value'] }}" value="{{ $status['value'] }}"
                                   @if(Request::has('status') && in_array($status['value'], Request::get('status'))) checked
                                   @endif type="checkbox" name="status[]"/>
                            <label for="{{ $status['value'] }}" class="mb-0">{{ $status['label'] }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group flex-column align-items-start flex-lg-row align-items-lg-center">
                <label for="sort" class="mr-1">Sortera efter</label>
                <semantic-dropdown id="sort" value-field="value"
                                   @if(Request::has('sort')) :initial-item="{{ json_encode(['value' => Request::get('sort')]) }}"
                                   @endif  placeholder="Välj sortering" form-name="sort"
                                   :data="{{ json_encode([['value' => 'order_status', 'label' => 'Order status'], ['value' => 'order_created', 'label' => 'Bokningsdatum']]) }}">
                    <template slot="dropdown-item" slot-scope="props">
                        <div class="item" :data-value="props.item.value">
                            <div class="item-text">@{{ props.item.label }}</div>
                        </div>
                    </template>
                </semantic-dropdown>
            </div>

        @endslot
    @endcomponent

    <div class="card card-block mx-0">
        @if($orders->count())
            <form action="{{ route('admin::orders.invoice_sent_many') }}" method="POST">
                {{ csrf_field() }}

                <div class="table">
                    @foreach($orders as $order)
                        <div class="table-row">
                            <div class="table-cell hidden-md-up more-button">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="{{ route('admin::orders.show', ['id' => $order->id]) }}">Visa</a>
                            </div>
                            <div class="table-cell col-md-4">
                                <div>
                                    <a href="{{ route('admin::orders.show', ['id' => $order->id]) }}">
                                        <strong>#{{ $order->id }}</strong>
                                        <span>av {{ $order->user ? $order->user->name : 'ta bort användare' }}</span>
                                    </a>
                                </div>
                                <div>{{ $order->created_at->formatLocalized('%Y-%m-%d, %H:%M') }}</div>

                                <a data-toggle="collapse" href="#collapse-{{ $order->id }}" class="d-flex align-items-center">
                                    <strong>Visa detaljer</strong>
                                    @icon('dropdown')
                                </a>
                            </div>
                            <div class="table-cell col-md-5">
                                @if($order->items->where('course_id', '!=', null)->first())
                                    <a href="{{ route('admin::courses.show', ['id' => $order->items->where('course_id', '!=', null)->first()->course_id]) }}">
                                        <strong>{{ $order->getFirstCourse() }} </strong> {{ $order->items->where('course_id', '!=', null)->first()->course->start_time->formatLocalized('%A %d:e %B, %Y (%H:%M)') }}
                                    </a><br>
                                @else
                                    <div>{{ $order->title }}</div>
                                @endif
                                @if (!$order->isGiftCardOrder() && $order->school)
                                    <a href="{{ route('admin::schools.show', ['id' => $order->school->id]) }}">{{ $order->school->name }}</a>
                                @endif
                            </div>
                            <div class="table-cell text-center col-md-3">
                                @if($order->handled)
                                    @if ($order->invoice_sent)
                                        <span class="text-success">Faktura skickad</span>
                                    @else
                                        <div class="form-checkbutton form-checkbutton-sm form-checkbutton-success">
                                            <input type="checkbox" name="order_id[]" id="invoice-sent-{{ $order->id }}"
                                                   value="{{ $order->id }}">
                                            <label for="invoice-sent-{{ $order->id }}">
                                                Markera faktura skickad @icon('check')
                                            </label>
                                        </div>
                                    @endif
                                @else
                                    @if($order->cancelled)
                                        <span class="text-danger">Avbokad</span>
                                    @endif
                                    @if($order->rebooked)
                                        <span class="text-warning">Ombokad</span>
                                    @endif
                                @endif
                            </div>
                            <div class="table-cell col-md-12 collapse @if(!$order->invoice_sent && $order->handled) in @endif"
                                 id="collapse-{{ $order->id }}">
                                <hr class="dashed">

                                @foreach($order->items as $item)
                                    {{ $item->quantity }}× <strong>{{ $item->name }} @if($item->isCourse() && $item->course->part) {{ trans('courses.part') }} {{ $item->course->part }} @endif</strong>
                                    á {{ $item->amount * $item->quantity  }} SEK
                                    @if($item->benefit && $item->benefit->benefit_type === \Jakten\Services\StudentLoyaltyProgramService::BENEFIT_TYPES['discount'])
                                        <strong>Bonus Saldo:</strong> {{$item->benefit->amount}} % | {{$item->amount * $item->benefit->amount / 100}} kr
                                    @endif
                                    @if($item->order->rebooked)
                                        <span class="text-warning font-weight-bold">Ombokad</span>
                                    @elseif($item->cancelled)
                                        <span class="text-danger font-weight-bold">Avbokad</span>
                                    @elseif($item->credited)
                                        <span class="text-success font-weight-bold">Krediterad</span>
                                    @endif<br>
                                @endforeach
                                @if($order->giftCardBalanceUsed() || $order->saldo_used || $order->discountBenefitUsed())
                                    Subtotal: {{ $order->order_value }} SEK<br>
                                    @if($order->giftCardBalanceUsed())
                                        Rabattkod: {{ $order->giftCardBalanceUsed() }} SEK<br>
                                    @endif
                                    @if($order->saldo_used)
                                        <strong>Bonus Saldo:</strong> {{ $order->cancelled ? 0 : 0 - $order->saldo_amount }} SEK<br>
                                    @endif
                                    @if($order->discountBenefitUsed())
                                        <strong>Bonus Saldo(discount):</strong> {{ $order->discountBenefitUsed() }} SEK<br>
                                    @endif
                                @endif
                                Totalt: {{ $order->order_value + $order->giftCardBalanceUsed()
                                            + abs($order->cancelled ? 0 : 0 - $order->saldo_amount)
                                            + $order->discountBenefitUsed()}} SEK<br>
                                <strong>Att fakturera: {{ $order->invoice_amount }} SEK</strong>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-sm-right">
                    <p>
                        Totalt ordervärde: <strong>{{ $orders->sum('order_value') }} SEK</strong><br>
                        Totalt att fakturera: <strong>{{ $orders->sum('invoice_amount') }} SEK</strong>
                    </p>
                    <button class="btn-success btn" type="submit">Spara</button>
                </div>
            </form>
        @else
            <no-results title="Inga beställningar hittades"></no-results>
        @endif
    </div>

    {!! $orders->render('pagination::bootstrap-4') !!}
@endsection
