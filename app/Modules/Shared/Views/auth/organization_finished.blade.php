@extends('shared::layouts.default')
@section('pageTitle', 'Tack för din registrering |')
@section('body-class', 'page-auth')
@section('robots')
    @if(config('app.env') === 'production')
        <!-- Facebook Pixel Code -->
        <script type="text/javascript">
            !function(f,b,e,v,n,t,s) {
                if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)
            }(window, document,'script', 'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '1700517896907439');
            fbq('track', 'CompleteRegistration');
        </script>
        <noscript>
            <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1700517896907439&ev=PageView&noscript=1" />
        </noscript><!-- End Facebook Pixel Code -->
    @endif
@stop
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                @include('shared::components.message')
                @include('shared::components.errors')
                <h2>Tack för din registrering!</h2>
            </div>
        </div>
    </div>
@endsection