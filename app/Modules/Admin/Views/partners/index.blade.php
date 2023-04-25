@extends('admin::layouts.default')
@section('body-class') page-dashboard @parent @stop

@section('content')

    <div class="card card-block">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{ route('admin::partners.create') }}" class="btn btn-primary">@icon('plus') Add Partner</a>
            </div>
        </div>
    </div>

    <div class="card card-block">
        <div class="table">
            <table class="table">

                <thead>
                <tr>
                    <td>#</td>
                    <td>Produkt/Tjänst</td>
                    <td>Partnernamn och kort beskrivning</td>
                    <td>URL</td>
                    <td>Synlig</td>
                    <td>Skapad</td>
                    <td colspan="3" class="centered"></td>
                </tr>
                </thead>

                @foreach($partners as $partner)
                    <tr>
                        <td>{{ $partner->id }}</td>
                        <td>{{ $partner->partner }}</td>
                        <td>{{ $partner->short_description }}</td>
                        <td>{{ $partner->url }}</td>
                        <td class="text-center">{{ svg_icon($partner->active ? 'check' : 'cross') }}</td>
                        <td>{{ $partner->created_at->formatLocalized('%Y-%m-%d, %H:%M') }}</td>
                        <td><a href="{{ route('admin::partners.edit', $partner->id) }}"
                               class="icon--link">@icon('edit')</a></td>
                        <td><a href="{{ route('admin::partners.destroy', $partner->id) }}" class="icon--link">@icon('dustbin')</a>
                        </td>
                    </tr>
                @endforeach

            </table>
        </div>
    </div>

    {!! $partners->render('pagination::bootstrap-4') !!}

@endsection
