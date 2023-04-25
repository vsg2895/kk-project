<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="form-group">
        <input type="hidden" name="type" value="{{ $type }}">
    </div>

    <div class="form-group">
        <small id="messageHelp" class="form-text text-muted">Endast synligt för admins.</small>
        <textarea class="form-control" aria-describedby="messageHelp" id="message" name="message" rows="3"></textarea>
    </div>

    <button class="btn btn-success" type="submit">Kommentera</button>
</form>