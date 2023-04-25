@if(!isset($type))
    <div class="form-group">
        <label for="role_id">Användartyp</label>
        <select class="custom-select" name="role_id" id="role_id">
            @foreach($roles as $label => $value)
                <option value="{{ $value }}" @if(\Jakten\Helpers\Roles::ROLE_ORGANIZATION_USER == $value && $initialOrganization) selected @endif>{{ trans('roles.'.$label) }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group" style="display: none;">
        <label for="organization_id">Organisation</label>
        <select class="custom-select" name="organization_id" id="organization_id">
            @foreach($organizations as $organization)
                <option value="{{ $organization->id }}" @if(old('organization_id', $initialOrganization) == $organization->id) selected @endif>{{ $organization->name }}</option>
            @endforeach
        </select>
    </div>
@endif

<div class="form-group @if($errors->has('email')) has-error @endif">
    <label for="email">E-post</label>
    <input class="form-control" type="email" name="email" id="email" value="{{ old('email') }}" />
</div>

<div class="form-group @if($errors->has('given_name')) has-error @endif">
    <label for="given_name">Förnamn</label>
    <input class="form-control" type="text" name="given_name" id="given_name" value="{{ old('given_name') }}" />
</div>

<div class="form-group @if($errors->has('family_name')) has-error @endif">
    <label for="family_name">Efternamn</label>
    <input class="form-control" type="text" name="family_name" id="family_name" value="{{ old('family_name') }}" />
</div>

<div class="form-group @if($errors->has('phone_number')) has-error @endif">
    <label for="phone_number">Telefonnummer</label>
    <input class="form-control" type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" />
</div>
