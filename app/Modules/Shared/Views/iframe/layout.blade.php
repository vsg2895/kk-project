<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="og:description" content="@yield('metaDescription', 'K&ouml;rkortsjakten &auml;r en oberoende
            prisj&auml;mf&ouml;relse av trafikskolor. Vi vill ge all information blivande f&ouml;rare beh&ouml;ver
            fram till den dagen de har k&ouml;rkortet i sin hand - allt från handledarkursen, valet av
            trafikskola till uppk&ouml;rningen')" id="ogde">

    <meta name="description" content="@yield('metaDescription', 'Körkortsjakten - en oberoende prisjämförelse av trafikskolor.
        Jämför skolor, boka kurser samt hitta all information Du behöver för Ditt körkort.')" id="htde">

    <meta property="og:image" content="/images/kkj-logo-new.png">

    <link rel="canonical" href="{{ url()->current() }}">
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-965101837"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
          integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

    <link href="{{ mix('/build/base.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ mix('/build/main.css') }}" rel="stylesheet" type="text/css">
    <script src="https://kit.fontawesome.com/3bb7927d0a.js" crossorigin="anonymous"></script>

</head>
<body class="@yield('body-white-class')">
<div id="app" class="iframe-payment-background">
    @yield('content')
</div>
<script type="text/javascript" src="{{ mix('/build/base.js') }}"></script>
</body>
</html>
