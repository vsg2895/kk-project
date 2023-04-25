{{ csrf_field() }}

<div class="form-group @if($errors->has('org_number')) has-error @endif">
    <label for="org_number">Organisationsnummer</label>
    <input class="form-control" type="text" name="org_number" id="org_number" value="{{ old('org_number') }}" />
</div>

<div class="form-group @if($errors->has('name')) has-error @endif">
    <label for="name">Namn på organisation</label>
    <input class="form-control" type="text" name="name" id="name" value="{{ old('name') }}" />
</div>

@include('shared::components.upload.logo')