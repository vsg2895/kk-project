@extends('admin::layouts.default')
@section('body-class') page-dashboard @parent @stop

@section('content')

    <div class="card card-block">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{ route('admin::course_type.create') }}" class="btn btn-primary">@icon('plus') Add Course Type</a>
            </div>
        </div>
    </div>

    <div class="card card-block">
        <div class="table">
            <table class="table">

                <thead>
                <tr>
                    <td>#</td>
                    <td>Name</td>
                    <td>Partnernamn och kort beskrivning</td>
                    <td>URL</td>
                    <td>Bestilles</td>
                    <td>Skapad</td>
                    <td colspan="3" class="centered"></td>
                </tr>
                </thead>

                @foreach($vehicleSegments as $vehicleSegment)
                    <tr>
                        <td>{{ $vehicleSegment->id }}</td>
                        <td>{{ $vehicleSegment->name }}</td>
                        <td>{{ $vehicleSegment->sub_description }}</td>
                        <td>{{ $vehicleSegment->slug }}</td>
                        <td class="text-center">{{ svg_icon($vehicleSegment->bookable ? 'check' : 'cross') }}</td>
                        <td>{{ $vehicleSegment->created_at ? $vehicleSegment->created_at->formatLocalized('%Y-%m-%d, %H:%M') : null }}</td>
                        <td><a href="{{ route('admin::course_type.edit', $vehicleSegment->id) }}"
                               class="icon--link">@icon('edit')</a>
                        </td>
                    </tr>
                @endforeach

            </table>
        </div>
    </div>

    {!! $vehicleSegments->render('pagination::bootstrap-4') !!}

@endsection
