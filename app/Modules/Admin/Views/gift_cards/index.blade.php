@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <h1 class="page-title">Presentkort</h1>
    </header>

    <div class="card card-block">
        <div class="col-md-6">
            <form method="POST" action="{{ route('admin::gift_cards.update') }}">
                {{ csrf_field() }}
                <label for="increased-value">Värde av presentkort i relation till pris</label>
                <div class="input-group">
                    <input id="increased-value" type="number" min="1" step="0.1" name="increasedValue" class="form-control" placeholder="Ökat värde på presentkort" value={{ $increasedValue }}>
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit">Uppdatera</button>
                </span>
                </div>
            </form>
        </div>
    </div>
@endsection
