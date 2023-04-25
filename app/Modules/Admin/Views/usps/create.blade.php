@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('admin::schools.index') !!}#usps">@icon('arrow-left') Tillbaka</a>
        <h1 class="page-title">Nytt erbjudande eller fördel</h1>
    </header>

    @include('shared::components.errors')

    <div class="card card-block">
        <form method="POST" action="{{ route('admin::usps.store') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <usps-form
                    :old-data="{{ json_encode(old()) }}"
                    :is-admin="true"
                    @if($school) :initial-school="{{ $school }}" @endif
            ></usps-form>
            <button class="btn btn-success" type="submit">Skapa</button>
        </form>
    </div>
@endsection


