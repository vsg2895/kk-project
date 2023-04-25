<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(config('prerender.enable') == true)
        <meta name="fragment" content="!">
    @endif
    <meta property="og:description" content="@yield('metaDescription', 'K&ouml;rkortsjakten &auml;r en oberoende
            prisj&auml;mf&ouml;relse av trafikskolor. Vi vill ge all information blivande f&ouml;rare beh&ouml;ver
            fram till den dagen de har k&ouml;rkortet i sin hand - allt från handledarkursen, valet av
            trafikskola till uppk&ouml;rningen')" id="ogde">

    <meta name="description" content="@yield('metaDescription', 'Körkortsjakten - en oberoende prisjämförelse av trafikskolor.
        Jämför skolor, boka kurser samt hitta all information Du behöver för Ditt körkort.')" id="htde">

    <meta property="og:image" content="/images/kkj-logo-new.png">

    <link rel="canonical" href="{{ url()->current() }}">

    <script src="https://use.typekit.net/qrc1sym.js"></script>
    <script>try {
            Typekit.load({async: true});
        } catch (e) {
        }</script>
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" 
   integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous"> 

    <link href="{{ mix('/build/base.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ mix('/build/main.css') }}" rel="stylesheet" type="text/css">
    <script src="https://kit.fontawesome.com/3bb7927d0a.js" crossorigin="anonymous"></script>

    <script>
        dataLayer = [];
    </script>

    @if(config('app.env') === 'production')
    <!-- Google Tag Manager -->
        <script>
            (function (w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start':
                        new Date().getTime(), event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', 'GTM-P57XLSS');
        </script>
        <!-- End Google Tag Manager -->

        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-965101837"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'AW-965101837');
        </script>

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-DCSRFNZP0N"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-DCSRFNZP0N');
        </script>
        <!-- Google tag (gtag.js) -->

        <!-- Adroll Pixel -->
        <script type="text/javascript">
            adroll_adv_id = "SDLNBB67WVCXLJFRIXGWGV";
            adroll_pix_id = "Q6MXEVWDYVEAPMPDYU55D3";
            (function () {
                try {
                    var _onload = function () {
                        if (document.readyState && !/loaded|complete/.test(document.readyState)) {
                            setTimeout(_onload, 10);
                            return
                        }
                        if (!window.__adroll_loaded) {
                            __adroll_loaded = true;
                            setTimeout(_onload, 50);
                            return
                        }
                        var scr = document.createElement("script");
                        var host = (("https:" === document.location.protocol) ? "https://s.adroll.com" : "http://a.adroll.com");
                        scr.setAttribute('async', 'true');
                        scr.type = "text/javascript";
                        scr.src = host + "/j/roundtrip.js";
                        ((document.getElementsByTagName('head') || [null])[0] ||
                            document.getElementsByTagName('script')[0].parentNode).appendChild(scr);
                    };

                    if (window.addEventListener) {
                        window.addEventListener('load', _onload, false);
                    } else {
                        window.attachEvent('onload', _onload)
                    }

                } catch (e) {

                }
            }());
        </script>
        <!-- End Adroll Pixel -->

        {{--Microsoft UET Tags script--}}
        <script>
            (function(w,d,t,r,u)
            {
                var f,n,i;
                w[u]=w[u]||[],f=function()
                {
                    var o={ti:"136020477"};
                    o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")
                },
                    n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function()
                {
                    var s=this.readyState;
                    s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)
                },
                    i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)
            })
            (window,document,"script","//bat.bing.com/bat.js","uetq");
            function uet_report_conversion() {
                window.uetq = window.uetq || [];
                window.uetq.push('event', 'PRODUCT_PURCHASE', {"ecomm_prodid":"REPLACE_WITH_PRODUCT_ID","ecomm_pagetype":"PURCHASE"});
            }
        </script>
        {{--Microsoft UET Tags script--}}

    @elseif(config('app.env') === 'staging' || config('app.env') === 'staging' || config('app.env') === 'test')
    <!-- Google Tag Manager -->
        <script>(function (w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start':
                        new Date().getTime(), event: 'gtm.js'
                });

                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s), dl = l !== 'dataLayer' ? '&l=' + l : '';
                j.async = true;

                j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', 'GTM-MMGWJCX');</script>
        <!-- End Google Tag Manager -->

        @include('shared::scripts.triggerBee.stub')
    @elseif(config('app.env') === 'local')
        @include('shared::scripts.triggerBee.stub')
    @endif

    @includeWhen(isset($user), 'shared::scripts.triggerBee.visitorIdentity')

    @if(false)
    <!--Start of Zendesk Chat Script-->
        <script type="text/javascript">
            window.$zopim || (function (d, s) {
                var z = $zopim = function (c) {
                    z._.push(c)
                }, $ = z.s =
                    d.createElement(s), e = d.getElementsByTagName(s)[0];
                z.set = function (o) {
                    z.set._.push(o)
                };
                z._ = [];
                z.set._ = [];
                $.async = !0;
                $.setAttribute("charset", "utf-8");
                $.src = "https://v2.zopim.com/?1XA99xLnE8zW7I9SKY2AeidWOkIWbFe3";
                z.t = +new Date;
                $.type = "text/javascript";
                e.parentNode.insertBefore($, e)
            })(document, "script");
        </script>
        <!--End of Zendesk Chat Script-->
    @endif

    @yield('robots')

    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>

    <script data-ad-client="ca-pub-4430930285623096" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>

    <title>@yield('pageTitle') Körkortsjakten</title>
</head>
<body class="@yield('body-class')">
{{--snowing effect, turn on before Christmas--}}
{{--@include('shared::components.snowing')--}}
<div id="google_translate_element"></div>
<!--[if lt IE 10]>
<div id="outdated">
    <h6>Din webbläsare stödjs ej längre!</h6>
    <p>Uppdatera din webbläsare för att webbplatsen ska visas korrekt. <br><a id="btnUpdateBrowser"
                                                                              href="http://outdatedbrowser.com/">Uppdatera
        min webbläsare nu</a></p>
    <p class="last"><a href="#" id="btnCloseUpdateBrowser" title="Stäng">&times;</a></p>
</div>
<![endif]-->

@if(config('app.env') === 'production')
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P57XLSS" height="0" width="0"
                style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->
@elseif(config('app.env') === 'staging' || config('app.env') === 'test')
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MMGWJCX"
                height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->
@endif

@section('header')
    @include('shared::components.header')
@show

<main id="page-main">
    <div class="page-main-inner {{ $checkoutPage ?? '' }}">
        @yield('sidebar') 

        <div id="app">
            @yield('main')  
        </div>
        @yield('no-vue')
    </div>
</main>

@section('footer')
 @include('shared::components.footer') 
@show

<script type="text/javascript" src="{{ mix('/build/base.js') }}"></script>
@yield('scripts')

@includeWhen(env('APP_ENV') === 'production', 'shared::components.hotjar')
<script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement(
            {
                pageLanguage: 'sv',
                includedLanguages: 'sv,en',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE
            },
            'google_translate_element');
    }
</script>

{{--    Google Translate script link--}}
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

{{--modal script--}}
@if(false)
<script>
    setTimeout(function () {
        if (getCookie('paketModal_is_shown') === null) {
            setCookie('paketModal_is_shown', 'false', 1);
        }
        var codeModal = document.getElementById('backtoschoolModal');
        if (getCookie('paketModal_is_shown') === 'false') {
            codeModal.style.display = "block";
            codeModal.style.top = "2 rem";
            codeModal.style.overflowY = "auto";
        }
        document.getElementById('close_backtoschoolModal').onclick = function() {
            codeModal.style.display = "none";
            setCookie('paketModal_is_shown', 'true', 1);
        }

        // document.getElementById('boka-har').onclick = function() {
        //     setCookie('paketModal_is_shown', 'true', 1);
        // }
    })

    function setCookie(name,value,days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }
</script>
@endif

@if(false)
<div class="modal" id="backtoschoolModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content backtoschoolModal-content">
            <div class="modal-header">
{{--                <h3 style="display: inline" class="modal-title" id="exampleModalLabel">Boka <b><a id="boka-har" href="/teoriprov-online">här</a></b></h3>--}}
                <button type="button" id="close_backtoschoolModal" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body d-flex justify-content-center">
{{--                <a href="/teoriprov-online" id="backtoschoolImg">--}}
                    <img src="{{asset('images/black-week.png')}}" alt="Korkortspaket">
{{--                </a>--}}
            </div>
        </div>
    </div>
</div>
@endif
</body>

@if(env('APP_ENV') === 'production' && (!isset($user) || ($user && !$user->isAdmin())))
<!-- Start of LiveChat (www.livechatinc.com) code -->
<script type="text/javascript">
    window.__lc = window.__lc || {};
    window.__lc.license = 12843249;
    ;(function(n,t,c){function i(n){return e._h?e._h.apply(null,n):e._q.push(n)};
        var e={_q:[],_h:null,_v:"2.0",on:function(){i(["on",c.call(arguments)])},once:function(){
                i(["once",c.call(arguments)])},off:function(){i(["off",c.call(arguments)])},
            get:function(){if(!e._h)throw new Error("[LiveChatWidget] You can't use getters before load.");
                return i(["get",c.call(arguments)])},call:function(){i(["call",c.call(arguments)])},init:function(){
                var n=t.createElement("script");
                n.async=!0,n.type="text/javascript",
                    n.src="https://cdn.livechatinc.com/tracking.js",t.head.appendChild(n)}};
        !n.__lc.asyncInit&&e.init(),n.LiveChatWidget=n.LiveChatWidget||e}(window,document,[].slice))
</script>
<noscript>
    <a href="https://www.livechatinc.com/chat-with/12843249/" rel="nofollow">Chat with us</a>,
    powered by <a href="https://www.livechatinc.com/?welcome" rel="noopener nofollow" target="_blank">LiveChat</a>
</noscript>
<!-- End of LiveChat code -->
@endif
</html>
