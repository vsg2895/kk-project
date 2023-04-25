@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('organization::schools.index') !!}#usps">@icon('arrow-left') Tillbaka</a>
        <h1 class="page-title">Redigera erbjudande eller fördel</h1>
    </header>

    @include('shared::components.errors')

    <div class="card card-block">
        <form method="POST" action="{{ route('organization::usps.update', ["id" => $usps->id]) }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <usps-form
                    :old-data="{{ json_encode(["text" => $usps->text]) }}"
                    :is-admin="false"
                    @if($school) :initial-school="{{ $school }}" @endif
            ></usps-form>
            <button class="btn btn-success" type="submit">Skapa</button>
        </form>
        <form class="mt-1" method="POST" action="{{ route('organization::usps.delete', $usps->id) }}">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <button type="submit" class="btn btn-outline-danger" data-confirm="@lang('form.confirm_action')">Ta bort</button>
        </form>
    </div>
@endsection


