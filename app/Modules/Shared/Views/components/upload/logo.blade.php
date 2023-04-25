<div class="form-group @if($errors->has('logo')) has-error @endif">
    <label for="logo">Logo</label>
    <small class="form-text text-muted">Välj en bild från din dator (png eller jpg), max 5MB.</small>
    <input type="file" class="form-control" id="logo" name="logo" accept="image/*">

    @if(isset($entity) && $entity->logo)
        <br><img src="{{ asset($entity->logo->path) }}" alt="">
    @endif
</div>
