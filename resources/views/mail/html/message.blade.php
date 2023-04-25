@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
<img class="logo" width="146" height="99" title="{{ config('app.name') }}" src="{{ url('/build/img/logo.png') }}"/>
        @endcomponent
@endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @slot('subcopy')
        @component('mail::subcopy')
            @if (isset($subcopy))
                {{ $subcopy }}
            @else
                Hälsningar från Körkortsjakten
            @endif
        @endcomponent
    @endslot

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            &copy; {{ config('app.name') }}
        @endcomponent
    @endslot
@endcomponent
