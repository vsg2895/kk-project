{{ csrf_field() }}

<div class="form-group @if($errors->has('org_number')) has-error @endif">
    <label for="org_number">Organisationsnummer</label>
    <input type="text" class="form-control" name="org_number" id="org_number" value="{{ old('org_number', $organization->org_number) }}" />
</div>

<div class="form-group @if($errors->has('name')) has-error @endif">
    <label for="name">Namn på organisation</label>
    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $organization->name) }}" />
</div>

<div class="form-group @if($errors->has('address')) has-error @endif">
    <label for="name">Registrerad adress</label>
    <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $organization->address) }}" />
</div>

@include('shared::components.upload.logo', ['entity' => $organization])
