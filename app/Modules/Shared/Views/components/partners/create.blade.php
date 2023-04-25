{{ csrf_field() }}

<div class="form-group @if($errors->has('partner')) has-error @endif">
    <label for="partner">Produkt/Tjänst</label>
    <input class="form-control" type="text" name="partner" id="partner" value="{{ old('partner') }}"/>
</div>

<div class="form-group @if($errors->has('short_description')) has-error @endif">
    <label for="short_description">Partnernamn och kort beskrivning</label>
    <textarea class="form-control" type="text" name="short_description"
              id="short_description">{{ old('short_description') }}</textarea>
</div>

<div class="form-group @if($errors->has('url')) has-error @endif">
    <label for="url">Partners URL</label>
    <input class="form-control" type="url" name="url" id="url" value="{{ old('url') }}"/>
</div>

<div class="form-group @if($errors->has('active')) has-error @endif">
    <input type="checkbox" name="active" id="active" checked="{{ old('active') }}">
    <label for="active">Aktiv</label>
</div>

<image-or-url-file file-label="Fil"></image-or-url-file>
