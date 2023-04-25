<div class="card card-block mx-0">
    <form class="form">
        <div class="row d-lg-flex align-items-lg-center">
            <div class="col-xs-12 col-lg-6">
                <input type="text" name="s" class="form-control" placeholder="Sök efter..." value="{{ Request::get('s') }}">
            </div>
            <div class="col-xs-12 col-lg-6">
                <small id="fileHelp" class="text-muted">
                    Exempel: <code>#3</code> / <code>#3, #5</code> / <code>#3, Trafikskola</code> / <code>"Fika Trafikskola AB"</code> / <code>@Gustav Svensson</code> {{ isset($examples) ? $examples : '' }}
                </small>
            </div>
        </div>

        {{ isset($addons) ? $addons : '' }}

        <button class="btn btn-primary mt-1" type="submit">Sök</button>
    </form>
</div>
