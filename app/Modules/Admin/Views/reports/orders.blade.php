@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <nav class="d-flex">
            <a class="back-button btn btn-sm btn-outline-primary" href="{{ route('admin::reports.index') }}">
                @icon('arrow-left')
                <span>Tillbaka</span>
            </a>
        </nav>
        <h1 class="page-title">Ordrar per skola och datum</h1>
    </header>
    @include('shared::components.message')
    @include('shared::components.errors')

    <form method="POST" action="{{ route('admin::reports.orders') }}">
        {{ csrf_field() }}

        <div class="card card-block mx-0">
            <reports-dates from="{{ request('start_time', '') }}" to="{{ request('end_time', '') }}"></reports-dates>

            <div class="form-group">
                <label for="sort">Sortera efter</label>
                <semantic-dropdown :search="true" :initial-item="{{ request('school', 0) }}" id="schools"
                                   placeholder="Välj skola" form-name="school" :data="{{ $schools }}">
                    <template slot="dropdown-item" slot-scope="props">
                        <div class="item" :data-value="props.item.id">
                            <div class="item-text">@{{ props.item.name }}</div>
                        </div>
                    </template>
                </semantic-dropdown>
            </div>

            <button type="submit" class="btn btn-primary">Sök</button>
        </div>

    </form>
    <div class="card card-block mx-0">
        @if($orders->count())
            <input hidden id="href-orders-orig" value="{{ route('admin::reports.download.orders', ['school' => request('school', null), 'start_time' => request('start_time', ''), 'end_time' => request('end_time', '')]) }}">
            <input hidden id="href-inv-orig" value="{{ route('admin::reports.download.invoice', ['school' => request('school', null), 'start_time' => request('start_time', ''), 'end_time' => request('end_time', '')]) }}">

            <a id="href-orders" target="_blank" href="{{ route('admin::reports.download.orders', ['school' => request('school', null), 'start_time' => request('start_time', ''), 'end_time' => request('end_time', ''), 'moms' => 25]) }}" class="btn btn-sm btn-primary pull-right">Download orders</a>
            <a id="href-inv" style="margin-right: 5px" target="_blank" href="{{ route('admin::reports.download.invoice', ['school' => request('school', null), 'start_time' => request('start_time', ''), 'end_time' => request('end_time', ''), 'moms' => 25]) }}" class="btn btn-sm btn-primary pull-right">Download invoice</a>

            @php($commission = 0)
            @php($payouts = 0)
            <table class="w-100 fs-12">
                <thead>
                <tr class="table-head table-row hidden-sm-down">
                    <th class="table-cell fs-12 col-md-1">Order id</th>
                    <th class="table-cell fs-12 col-md-1">Bokningsdatum</th>
                    <th class="table-cell fs-12 col-md-1">Kursdatum</th>
                    <th class="table-cell fs-12 col-md-1">Kurs</th>
                    <th class="table-cell fs-12 col-md-2">Användarnamn</th>
                    <th class="table-cell fs-12 col-md-1">Köparen</th>
                    <th class="table-cell fs-12 col-md-1">Summa</th>
                    <th class="table-cell fs-11 col-md-1">Provision</th>
                    <th class="table-cell fs-11 col-md-1">Klarna Fee</th>
                    <th class="table-cell fs-12 col-md-2">Payout</th>
                </tr>
                </thead>
                <tbody>
                @foreach($groups as $group)
                    @foreach($group as $order)
                    <tr class="table-row">
                        <td class="table-cell col-md-1">
                            <a href="{{ route('admin::orders.show', ['id' => $order->order_id]) }}">#{{ $order->order_id }}</a>
                        </td>
                        <td class="table-cell col-md-1">
                            {{ $order->booking_date }}
                        </td>
                        <td class="table-cell col-md-1">
                            {{ $order->course_date }}
                        </td>
                        <td class="table-cell col-md-1">
                            @if (strpos($order->type, '_') !== false)
                                {{ trans('vehicle_segments.' . strtolower($order->type)) }}
                            @else
                                {{ $order->type }}
                            @endif
                        </td>
                        <td class="table-cell col-md-2">
                            {{ $order->user_name }}<br>
                        </td>
                        <td class="table-cell col-md-1">
                            {{ $order->buyer }}
                        </td>
                        <td class="table-cell col-md-1">
                            {{ $order->amount }}
                        </td>
                        @php($orderCommission = $order->amount * ($order->provision/100))
                        @php($orderPayout = $order->amount - $orderCommission)

                        <td class="table-cell col-md-1">
                            {{ $orderCommission }}
                        </td>
                        <td class="table-cell col-md-1">
                            {{ number_format($order->amount * (config('fees.klarna')/100), 2, ',', ' ') }}
                        </td>
                        <td class="table-cell col-md-2">
                            {{ number_format($orderPayout - $order->amount * (config('fees.klarna')/100), 2, ',', ' ') }}
                        </td>
                        @php($commission += $orderCommission)
                        @php($payouts += $orderPayout)
                    </tr>
                @endforeach
                @endforeach
                </tbody>
            </table>
            <div class="table">
                <div class="table-row">
                    <div class="table-cell col-md-2"><strong>Total summa</strong></div>
                    <div class="table-cell offset-md-5 col-md-1"><strong>{{ $orders->sum('total_sum_of_order') }}
                           </strong></div>
                    <div class="table-cell col-md-1"><strong>{{ $commission }}</strong></div>
                    <div class="table-cell col-md-1"><strong>{{ number_format($orders->sum('total_sum_of_order') * (config('fees.klarna')/100), 2, ',', ' ') }}</strong></div>
                    <div class="table-cell col-md-2"><strong>{{ number_format($payouts - $orders->sum('total_sum_of_order') * (config('fees.klarna')/100), 2, ',', ' ') }}</strong></div>

                </div>
            </div>
        @else
            <no-results title="Beställningar hittades inte"></no-results>
        @endif
    </div>


@endsection

@section('scripts')
    <script lang="text/javascript">
        $("#moms").keyup(function() {

            $("#href-orders").attr("href", $("#href-orders-orig").val() + '&moms=' + $(this).val());
            $("#href-inv").attr("href", $("#href-inv-orig").val() + '&moms=' + $(this).val());

        });
    </script>
@endsection
