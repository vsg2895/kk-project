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

    <form method="POST" action="{{ route('admin::reports.schools') }}">
        {{ csrf_field() }}

        <div class="card card-block mx-0">
            <reports-dates from="{{ request('start_time', '') }}" to="{{ request('end_time', '') }}"></reports-dates>

            <button type="submit" class="btn btn-primary">Sök</button>
        </div>

    </form>
    <div class="card card-block mx-0">
        @if($schools->count())
            <div class="table">
                <div class="table-head table-row hidden-sm-down">
                    <div class="table-cell col-md-3">SKOLNAMN</div>
                    <div class="table-cell col-md-2">Totala summan</div>
                    <div class="table-cell col-md-2">Provision</div>
                    <div class="table-cell col-md-2">Klarna Fee</div>
                    <div class="table-cell col-md-1">Utbetalningar</div>
                    <div class="table-cell col-md-1 float-right">Bokningsavgift</div>
                </div>
                @php($commission = 0)
                @php($payouts = 0)
                @php($booking_fee = 0)
                @foreach($schools as $school)
                    <div class="table-row">
                        <div class="table-cell col-md-3"><a href="{{ route('admin::reports.orders', ['school' => $school->id, 'start_time' => request()->get('start_time'), 'end_time' => request()->get('end_time')]) }}">{{ $school->name }}</a></div>
                        <div class="table-cell col-md-2">{{ $school->total }}</div>

                        @php($commission += $school->school_commission)

                        <div class="table-cell col-md-2">{{ $school->total - $school->school_commission }}</div>

                        <div class="table-cell col-md-2">{{ number_format($school->total * (config('fees.klarna')/100), 2, ',', ' ') }} </div>

                        <div class="table-cell col-md-1">{{ number_format($school->school_commission - ($school->total * (config('fees.klarna')/100)), 2, ',', ' ') }}</div>

                        <div class="table-cell col-md-1 float-right">{{ number_format($school->booking_fee, 2, ',', ' ') }}</div>

                        @php($booking_fee += $school->booking_fee)
                    </div>
                @endforeach
                <div class="table-row">
                    <div class="table-cell col-md-3"><strong>Totala summan</strong></div>
                    <div class="table-cell col-md-2"><strong>{{ number_format($schools->sum('total') + $booking_fee, 2, ',', ' ') }}</strong></div>
                    <div class="table-cell col-md-2"><strong>{{ number_format($schools->sum('total') - $commission, 2, ',', ' ') }}</strong></div>
                    <div class="table-cell col-md-2"><strong>{{ number_format($schools->sum('total') * ((config('fees.klarna')/100)), 2, ',', ' ') }}</strong></div>
                    <div class="table-cell col-md-1"><strong>{{ number_format($commission - ($schools->sum('total') * ((config('fees.klarna')/100))), 2, ',', ' ') }}</strong></div>
                    <div class="table-cell col-md-1 float-right"><strong>{{ number_format($schools->sum('booking_fee'), 2, ',', ' ') }}</strong></div>
                </div>
            </div>
        @else
            <no-results title="Beställningar hittades inte"></no-results>
        @endif
    </div>

@endsection
