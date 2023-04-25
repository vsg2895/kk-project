<div class="form-group @if($errors->has('given_name')) has-error @endif">
    <label for="given-name">Förnamn</label>
    <input class="form-control" type="text" id="given-name" name="given_name" value="{{ old('given_name', $user->given_name) }}" />
</div>

<div class="form-group @if($errors->has('family_name')) has-error @endif">
    <label for="family-name">Efternamn</label>
    <input class="form-control" type="text" id="family-name" name="family_name" value="{{ old('family_name', $user->family_name) }}" />
</div>

<div class="form-group @if($errors->has('email')) has-error @endif">
    <label for="email">E-post</label>
    <input class="form-control" type="text" id="email" value="{{ old('email', $user->email) }}" readonly />
</div>

<div class="form-group @if($errors->has('phone_number')) has-error @endif">
    <label for="phone-number">Telefonnummer</label>
    <input class="form-control" type="text" id="phone-number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" />
</div>
