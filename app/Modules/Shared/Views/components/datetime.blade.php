@if($date)
    @if(isset($showOriginal) && $showOriginal)
        <time class="time-tag" datetime="{{ $date }}">
            {{ $date }}
        </time>
    @else
        @if(Carbon\Carbon::parse($date)->diffInMonths() > 1)
            <time class="time-tag" datetime="{{ $date }}">
                {{ $date }}
            </time>
        @else
            <time class="time-tag" datetime="{{ $date }}" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="{{ $date }}">
                {{ Carbon\Carbon::parse($date)->diffForHumans() }}
            </time>
        @endif
    @endif
@endif
