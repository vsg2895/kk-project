<style>
    body {
        font-size: 0.6rem;
        font-family: Roboto, "Noto Sans", Arial, sans-serif;
    }

    table {
        margin-bottom: 5rem;
        width: 100%;
    }
</style>
@if($orders->count())
    @php($commission = 0)
    @php($payouts = 0)
    <table class="w-100">
        <thead>
        <tr class="table-head table-row hidden-sm-down">
            <th class="table-cell col-md-1" style="text-align: left;">{{ 'ORDER ID' }}</th>
            <th class="table-cell col-md-2" style="text-align: left;">{{ 'BOKNINGSDATUM' }}</th>
            <th class="table-cell col-md-1" style="text-align: left;">{{ 'KURSDATUM' }}</th>
            <th class="table-cell col-md-1" style="text-align: left;">{{ 'KURS' }}</th>
            <th class="table-cell col-md-2" style="text-align: left;">{{ 'DELTAGARE' }}</th>
            <th class="table-cell col-md-1" style="text-align: left;">{{ 'BESTÄLLARE' }}</th>
            <th class="table-cell col-md-1" style="text-align: left;">{{ 'SUMMA' }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($orderItems as $items)
            @foreach($items as $item)
            <tr class="table-row">
                <td class="table-cell col-md-1">
                    #{{ $item->order_id }}
                </td>
                <td class="table-cell col-md-2">
                    {{ $item->booking_date }}
                </td>
                <td class="table-cell col-md-1">
                    {{ $item->course_date }}
                </td>
                <td class="table-cell col-md-1">
                        @if (strpos($item->type, '_') !== false)
                            {{ trans('vehicle_segments.' . strtolower($item->type)) }}
                        @else
                            {{ $item->type }}
                        @endif
                </td>
                <td class="table-cell col-md-2">
                        {{ $item->user_name }}
                </td>
                <td class="table-cell col-md-1">
                    {{ $item->buyer }}
                </td>
                <td class="table-cell col-md-1">
                    {{ number_format($item->amount, 2, ',', ' ') }} kr
                </td>
            </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
    @php($totalSum = $orders->sum('total_sum_of_order'))
    @php($klarnaCommision = $totalSum * (config('fees.klarna')/100))
    <table>
        <tr>
            <td>Summa bokningar:</td>
            <td></td>
            <td style="text-align: right">{{ number_format($totalSum, 2, ',', ' ') }} kr</td>
        </tr>
        <tr>
            <td>Transaktionsavgift Klarna:</td>
            <td >1,8%</td>
            <td style="text-align: right; color: red;">{{ number_format($klarnaCommision, 2, ',', ' ') }} kr</td>
        </tr>

        @foreach($data['types'] as $key => $type)
            <tr>
                <td>
                    @if (strpos($key, '_') !== false)
                        {{ trans('vehicle_segments.' . strtolower($key)) }}:
                    @else
                        {{ $key }}:
                    @endif
                </td>
                <td>{{ $type['prov'] }}%</td>
                <td style="text-align: right; color: red;">{{ number_format($type['val'], 2, ',', ' ') }} kr</td>
            </tr>
        @endforeach
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr>
            <td class="bold">Utbetalning:</td>
            <td ></td>
            <td style="text-align: right">{{ number_format($data['total'] - $klarnaCommision, 2, ',', ' ') }} kr</td>
        </tr>
    </table>
@endif


