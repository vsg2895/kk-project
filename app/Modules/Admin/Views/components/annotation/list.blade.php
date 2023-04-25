@if(count($list))
    <ul class="list-group">
        @foreach($list as $item)
            <li class="list-group-item list-group-item-action item-{{ str_replace('.', '-', $item->type) }}">
                <span class="tag tag-default tag-pill pull-right">
                    @include('shared::components.datetime', ['date' => $item->created_at])
                </span>

{{--                @if($item->type == Jakten\Services\Asset\AnnotationType::KLARNA_ONBOARDING_ERROR)--}}
                @if($item->type == Jakten\Services\Annotation\AnnotationType::KLARNA_ONBOARDING_ERROR)

                    <h5>Klarna Onboarding Error</h5>

                    @if($item->message)
                        <p>{{ $item->message }}</p>
                    @endif

                    <ul class="klarna-errors">
                        @foreach($item->data['errors'] as $key => $value)
                            <li><strong>{{ $key }}</strong> {{ $value }}</li>
                        @endforeach
                    </ul>

                @else
                    <small class="text-muted">{{ $item->user->given_name }} {{ $item->user->family_name }} skrev</small>

                    @if($item->message)
                        <p>{{ $item->message }}</p>
                    @endif
                @endif
            </li>
        @endforeach
    </ul>
@else
    <p class="text-muted text-center">Inga kommentarer hittades</p>
@endif
