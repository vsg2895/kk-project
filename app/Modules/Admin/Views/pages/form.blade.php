{{ csrf_field() }}

<div class="form-group">
    <label for="title">Länk</label>
    <div class="input-group">
        <span class="input-group-addon">{{ url('/') }}</span>
        <input class="form-control" id="uri" name="uri" aria-describedby="uriHelp" placeholder="Skriv uri länk"
               value="{{ old('uri', isset($page) ? $page->getUri() : null) }}">
    </div>
    <small id="titleHelp" class="form-text text-muted">Länk ska börja med <code>/</code>, t.ex. <code>/kontakt</code>
    </small>
</div>

<div class="form-group">
    <label for="title">Titel</label>
    <input class="form-control" id="title" name="title" aria-describedby="titleHelp" placeholder="Skriv titel"
           value="{{ old('title', isset($page) ? $page->title : null) }}">
</div>

<div class="form-group">
    <label for="meta">Meta beskrivning</label>
    <input class="form-control" id="meta_description" name="meta_description" aria-describedby="metaHelp"
           placeholder="Skriv beskrivning"
           value="{{ old('meta_description', isset($page) ? $page->meta_description : null) }}">
</div>

<div class="form-group">
    <label for="content">Innehåll</label>
    <textarea class="form-control" aria-describedby="contentHelp" id="content" name="content"
              rows="3">{{ old('content', isset($page) ? $page->content : null) }}</textarea>
</div>

<button class="btn btn-success" type="submit">{{ isset($page) ? 'Uppdatera' : 'Skapa' }}</button>
