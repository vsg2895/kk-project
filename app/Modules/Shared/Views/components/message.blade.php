@if(isset($message) || Session::has('message'))
    <div class="alert alert-success" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Stäng">
            <span aria-hidden="true">&times;</span>
        </button>
        @if(isset($title))
            <strong>{{ $title }}</strong>
        @endif
        {{ isset($message) ? $message :  Session::get('message')}}
    </div>
@endif