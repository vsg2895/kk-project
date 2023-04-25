@if(isset($info) || Session::has('info'))
    <div class="alert alert-info" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Stäng">
            <span aria-hidden="true">&times;</span>
        </button>
        @if(isset($title))
            <strong>{{ $title }}</strong>
        @endif
        {{ isset($info) ? $info :  Session::get('info')}}
    </div>
@endif