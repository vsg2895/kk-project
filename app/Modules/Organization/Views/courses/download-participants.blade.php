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
@if($bookings->count())
    @php($commission = 0)
    @php($payouts = 0)
    <table class="w-100">
        <thead>
        <tr class="table-head table-row hidden-sm-down">
            <th class="table-cell col-md-1" style="text-align: left;">{{ 'ORDER NR' }}</th>
            <th class="table-cell col-md-1" style="text-align: left;">{{ 'KURSDATUM' }}</th>
            <th class="table-cell col-md-1" style="text-align: left;">{{ 'KURS' }}</th>
            <th class="table-cell col-md-2" style="text-align: left;">{{ 'DELTAGARE' }}</th>
            <th class="table-cell col-md-1" style="text-align: left;">{{ 'PERSONNUMMER' }}</th>
            <th class="table-cell col-md-1" style="text-align: left;">{{ 'EMAIL' }}</th>
            <th class="table-cell col-md-1" style="text-align: left;">{{ 'SUMMA' }}</th>

        </tr>
        </thead>
        <tbody>
        @foreach($bookings as $items)
            @foreach($items as $item)
            <tr class="table-row">
                <td class="table-cell col-md-1">
                    #{{ $item->order_id }}
                </td>
                <td class="table-cell col-md-2">
                    {{ $item->course->start_time }}
                </td>
                <td class="table-cell col-md-1">
                        @if (strpos($item->type, '_') !== false)
                            {{ trans('vehicle_segments.' . strtolower($item->type)) }}
                        @else
                            {{ $item->type }}
                        @endif
                </td>
                <td class="table-cell col-md-2">
                        {{ $item->participant->name }}
                </td>
                <td class="table-cell col-md-1">
                    {{ $item->participant->social_security_number }}
                </td>
                <td class="table-cell col-md-1">
                    {{ $item->participant->email }}
                </td>
                <td class="table-cell col-md-1">
                    {{ number_format($item->amount, 2, ',', ' ') }} kr
                </td>
            </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
@endif


