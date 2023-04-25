@extends('shared::layouts.default')
@section('robots')
    <meta name="robots" content="noindex">
@stop
@section('content')
    <div id="booking-confirmation" class="container text-xs-center">
        <h1 class="card-title">Bokning genomförd</h1>

        <p>Tack för din beställning. Tänk på att vara i god tid till din lektion och glöm inte legitimation. Vi ber dig uppmärksamma att ibland hamnar orderbekräftelse i mailens skräppost. För att administrera din bokning och ta del av fler förmånliga erbjudanden loggar in på sidan,
            <br/>Lycka till!</p>

        @if(!$klarnaOrder)
            <p>Glöm inte bort att ta med dig betalmedel, betalning sker när du kommer till {{ $course->school->name }}.</p>
        @endif
        <br>

        <a class="btn btn-primary" href="{{ route('shared::introduktionskurs', ['citySlug' => $course->city->slug]) }}">Hitta fler kurser i {{ $course->city->name }}</a>
    </div>
@endsection
@section('no-vue')
    @if($klarnaOrder)
        <div>
            {!! $klarnaOrder['html_snippet'] !!}
        </div>

        @if(config('app.env') === 'production' || config('app.env') === 'staging' || config('app.env') === 'test')
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

            @if(config('app.env') === 'production')
                ga('create', 'UA-6069984-5', 'auto', {'name': 'klarnaOrder'});
            @else
                ga('create', 'UA-76305829-2', 'auto', {'name': 'klarnaOrder'});
            @endif

            ga('klarnaOrder.require', 'ecommerce');
            @foreach ($klarnaOrder['order_lines'] as $item)
            ga('klarnaOrder.ecommerce:addItem', {
                'name': '{{ $item["name"] }}',
                'sku': '{{ $item["reference"] }}',
                'price': {{ $item['total_amount'] / 100 }},
                'quantity': {{ $item['quantity'] }}
            });
            @endforeach

            ga('klarnaOrder.ecommerce:addTransaction', {
                'id': '{!! env('KLARNA_V3_KKJ_PAYMENT_ID') !!}',
                'revenue': {!! $klarnaOrder["order_amount"] / 100 !!},
                'currency': 'SEK'
            });

            gtag('event', 'conversion', {
                'send_to': 'AW-965101837/NIo5CPmI-5ABEI2SmcwD',
                'value': {!! $klarnaOrder["order_amount"] / 100 !!},
                'currency': 'SEK',
                'transaction_id': ''
            });

            ga('klarnaOrder.ecommerce:send');
            ga('klarnaOrder.ecommerce:clear');
        </script>
        @endif

        @if(config('app.env') === 'local' || config('app.env') === 'test')
            <form method="POST" action="{{ $klarnaOrder['merchant']['push_uri'] }}">
                <button class="btn btn-primary" type="submit">PUSH FROM KLARNA</button>
            </form>
        @endif

        @if(config('app.env') === 'production')
            <!-- Facebook Pixel Code -->
            <script>
                !function(f,b,e,v,n,t,s)
                {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                    n.queue=[];t=b.createElement(e);t.async=!0;
                    t.src=v;s=b.getElementsByTagName(e)[0];
                    s.parentNode.insertBefore(t,s)}(window, document,'script',
                    'https://connect.facebook.net/en_US/fbevents.js');
                fbq('init', '1700517896907439');
                fbq('track', 'PageView');
            </script>
            <noscript>
                <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1700517896907439&ev=PageView&noscript=1" />
            </noscript>
            <!-- End Facebook Pixel Code -->
            <script>
                fbq('track', 'Purchase', {
                    value: {!! $klarnaOrder["order_amount"] / 100 !!},
                    currency: 'SEK'
                });
            </script>
        @endif
    @endif
@endsection

