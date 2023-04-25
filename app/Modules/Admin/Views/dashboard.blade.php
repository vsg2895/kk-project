@extends('admin::layouts.default')
@section('body-class') page-dashboard @parent @stop

@section('content')
    <form method="GET" action="{{ route('admin::dashboard') }}">
        <div class="card card-block mx-0">
            <div class="d-flex">
                <div style="margin-right: 15px;">
                    <reports-dates from="{{ request('start_time', '') }}" to="{{ request('end_time', '') }}"></reports-dates>
                </div>
                <div style="flex-grow: 1;">
                    <div class="mt-2">
                        <input id="include_booking_fee"  type="checkbox" name="include_booking_fee" @if(request()->include_booking_fee) checked @endif/>
                        <label for="include_booking_fee">Include Booking Fee</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Sök</button>
        </div>
    </form>
    <div class="row">
        <div class="col-sm-12 col-md-6">
            <div class="card card-block">
                <h3 class="mb-0">Total Amount:</h3>
                @php
                    $compared = round($filterData['current_year']['total_amount'] * 100 / max($filterData['prev_year']['total_amount'], 1), 2) - 100;
                @endphp
                <div class="display-5 text-success">{{ $filterData['current_year']['total_amount_formatted'] }} SEK
                    <span class="{{$compared > 0 ? 'text-success' : 'text-danger'}}">({{$compared}} %)</span>
                </div>
                <div class="display-6">{{ $filterData['prev_year']['total_amount_formatted'] }} SEK</div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <div class="card card-block">
                <h3 class="mb-0">Count:</h3>
                @php
                    $compared = round($filterData['current_year']['order_count'] * 100 / max($filterData['prev_year']['order_count'], 1), 2) - 100;
                @endphp
                <div class="display-5 text-success">
                    {{ $filterData['current_year']['order_count'] }}
                    <span class="{{$compared > 0 ? 'text-success' : 'text-danger'}}">({{$compared}} %)</span>
                </div>
                <div class="display-6">{{ $filterData['prev_year']['order_count'] }} </div>
            </div>
        </div>
    </div>
@endsection
