<div class="landing-block {{ $class }}">
    <div class="landing-block-img-wrapper">
        <div class="landing-block-img {{$imageClass}}" style="background-image: url({{ url($image) }})"></div>
    </div>
    <div class="landing-block-card">
        <h2 class="landing-block-h2">{!! $title !!}</h2>
        <div class="landing-block-content">
            <h3 class="lineHeight landing-block-h3">{!! $content !!}</h3>
            <div class="landing-block-button-wrapper">
                @if(!is_array($btnText))
                    <a class="btn-main" href="{{ $route }}" role="button">{!! $btnText !!}</a>
                @else
                    @foreach($btnText as $key => $btnRoute)
                        <a class="btn-main" href="{{ $btnRoute }}" role="button">{!! $key !!}</a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
