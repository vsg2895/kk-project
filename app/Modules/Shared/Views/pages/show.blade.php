@extends('shared::layouts.default')
@if($page->meta_description)
    @section('metaDescription')
{{ $page->meta_description }}
    @endsection
@endif
@if($page->title)
    @section('pageTitle')
        @if ($page->title != "")
            {{ $page->title }} |
        @endif
    @endsection
@endif
@section('content')
    <script lang="text/javascript">
    </script>
<div class="page-pages">
    @if(Request::is('korkort*'))
    <div class="container sidebar">
    <aside class="col-sm-12 col-md-4">
        <h2 class="text-uppercase"><a href="{{ url('/korkort') }}"><em>Vägen till körkort</em></a></h2>
        <ul class="list-unstyled">
            <li class="item">
                <a class="nav-link {{ Request::is('korkort/korkortstillstand') ? 'active' : '' }}" href="{{ url('/korkort/korkortstillstand') }}">1. Körkortstillstånd</a>
            </li>
            <li class="item">
                <a class="nav-link {{ Request::is('korkort/hitta-trafikskola') ? 'active' : '' }}" href="{{ url('/korkort/hitta-trafikskola') }}">2. Hitta trafikskola</a>
            </li>
            <li class="item">
                <a class="nav-link {{ Request::is('korkort/ovningskora') ? 'active' : '' }}" href="{{ url('/korkort/ovningskora') }}">3. Övningsköra</a>
            </li>
            <li class="item">
                <a class="nav-link {{ Request::is('korkort/korkortsteori') ? 'active' : '' }}" href="{{ url('/korkort/korkortsteori') }}">4. Körkortsteori</a>
            </li>
            <li class="item">
                <a class="nav-link {{ Request::is('korkort/lektioner-intensivkurs') ? 'active' : '' }}" href="{{ url('/korkort/lektioner-intensivkurs') }}">5. Körlektioner/Intensivkurs</a>
            </li>
            <li class="item">
                <a class="nav-link {{ Request::is('korkort/riskettan') ? 'active' : '' }}" href="{{ url('/korkort/riskettan') }}">6. Riskettan</a>
            </li>
            <li class="item">
                <a class="nav-link {{ Request::is('korkort/risktvaan') ? 'active' : '' }}" href="{{ url('/korkort/risktvaan') }}">7. Risktvåan</a>
            </li>
            <li class="item">
                <a class="nav-link {{ Request::is('korkort/teoriprov') ? 'active' : '' }}" href="{{ url('/korkort/teoriprov') }}">8. Teoriprov</a>
            </li>
            <li class="item">
                <a class="nav-link {{ Request::is('korkort/uppkorning') ? 'active' : '' }}" href="{{ url('/korkort/uppkorning') }}">9. Uppkörning</a>
            </li>
        </ul>
        <hr>
        <ul class="list-unstyled">
            <li class="item">
                <a class="nav-link {{ Request::is('korkort/a-mc') ? 'active' : '' }}" href="{{ url('/korkort/a-mc') }}">Körkort för MC</a>
            </li>
            <li class="item">
                <a class="nav-link {{ Request::is('korkort/am-moped') ? 'active' : '' }}" href="{{ url('/korkort/am-moped') }}">Körkort för Moped</a>
            </li>
        </ul>
    </aside>
    @endif
    {!! $page->content !!}
    @if(Request::is('korkort*'))
        </div>
    @endif

</div>

@endsection

@section('scripts')
    <script lang="text/javascript">
        $(function() {

            //BEGIN
            $(".accordion__title").on("click", function(e) {

                e.preventDefault();
                var $this = $(this);

                if (!$this.hasClass("accordion-active")) {
                    $(".accordion__content").slideUp(400);
                    $(".accordion__title").removeClass("accordion-active");
                    $('.accordion__arrow').removeClass('accordion__rotate');
                }

                $this.toggleClass("accordion-active");
                $this.next().slideToggle();
                $('.accordion__arrow',this).toggleClass('accordion__rotate');
            });
            //END

        });
    </script>
@endsection
