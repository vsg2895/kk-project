<div class="jumbotron jumbotron-light">
    <h3>Ändra Lösenord</h3>

    <div class="form-group @if($errors->has('password_old')) has-error @endif">
        <label for="password_old">Nuvarande lösenord</label>
        <input class="form-control simple-input" id="password_old" name="password_old" type="password">
    </div>

    <div class="form-group @if($errors->has('password')) has-error @endif">
        <label for="password">Nytt lösenord</label>
        <input class="form-control simple-input" id="password" name="password" type="password">
    </div>

    <div class="form-group">
        <label for="password_confirmation">Bekräfta nytt lösenord</label>
        <input class="form-control simple-input" id="password_confirmation" name="password_confirmation" type="password">
    </div>
</div>