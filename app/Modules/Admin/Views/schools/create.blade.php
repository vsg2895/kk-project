@extends('admin::layouts.default')
@section('content')
    <header class="section-header">
        <a class="back-button btn btn-sm btn-outline-primary" href="{!! URL::previous('admin::schools.index') !!}">@icon('arrow-left') Tillbaka</a>
        <h1 class="page-title">Ny trafikskola</h1>
    </header>

    @include('shared::components.errors')

    <div class="card card-block">
        <form method="POST" action="{{ route('admin::schools.store') }}" enctype="multipart/form-data">
            <div class="form-group">
                <label for="organization_id">Organisation</label>
                <select class="custom-select" id="organization-id" name="organization_id">
                    <option value="">--- Ingen organisation ---</option>
                    @foreach($organizations as $organization)
                        <option value="{{ $organization->id }}" @if(old('organization_id', $initialOrganization) == $organization->id) selected @endif>{{ $organization->name }}</option>
                    @endforeach
                </select>
            </div>
            @include('shared::components.school.create')
            <button class="btn btn-success" type="submit">Skapa</button>
        </form>
    </div>
@endsection
@section('scripts')
    @parent
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_API_KEY') }}&libraries=places&language=sv&region=se"></script>
@endsection

