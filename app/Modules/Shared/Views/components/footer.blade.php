<footer id="page-footer">
    <script
        async
        src="https://eu-library.klarnaservices.com/lib.js"
        data-client-id="4f3f0145-a7b7-5be0-879c-c6b6eb8918d8"
    ></script>
    <div class="page-footer-inner">
        <div class="d-flex flex-column justify-content-center align-items-center mb-2">
            <klarna-placement data-key="footer-promotion-auto-size" data-locale="en-SE"></klarna-placement>
            <div class="">
                <img style="height: 32px" src="{{asset('build/img/swish-logo.svg')}}" alt="swish-logo">
                <img style="height: 50px" src="{{asset('build/img/bankID-logo.svg')}}" alt="bankID-logo">
            </div>
        </div>
        <div class="road road-right">
            <img src="/build/img/road-right.png">
        </div>
        <div class="container row-flex mt-md-2">
            <div class="col-xs-12 col-md-4 text-xs-center text-md-left order-md-1 order-2 mb-2 px-0">
                <nav class="nav text-left">
                    <div class="col-xs-6 col-md-12 d-md-flex flex-md-column">
                        <a class="vagen-till-korkort-m" href="{{ url('/korkort') }}">Vägen till körkort</a>
                        <a class="vagen-till-korkort-m" href="{{ url('/medlemskap') }}">Medlemskap</a>
                        <a class="vagen-till-korkort-m" href="{{ url('/klarna') }}">Mer om Klarna</a>
                        <a class="vagen-till-korkort-m" href="{{ url('/villkor') }}">Användarvillkor och Personuppgiftspolicy</a>
                    </div>
                    <div class="col-xs-6 col-md-12 d-md-flex flex-md-column">
                        <a class="vagen-till-korkort-m" href="{{ url('/villkor-trafikskola') }}">Avtalsvillkor med trafikskolor</a>
                        <a class="vagen-till-korkort-m" href="{{ url('/kopvillkor') }}">Köpvillkor</a>
                        <a class="vagen-till-korkort-m" href="{{ url('/faq') }}">Vanliga frågor</a>
                        <a class="vagen-till-korkort-m" href="{{ url('/friskrivning') }}">Friskrivningsklausul</a>
                        <a class="vagen-till-korkort-m" href="{{ url('/om-oss') }}">Om oss</a>
                        <a class="vagen-till-korkort-m" href="{{ route('shared::pages.contact') }}">Kontakta oss</a>
                    </div>
                </nav>
            </div>

            <div class="col-xs-12 col-md-4 text-xs-center order-md-2 order-3 mb-2">
                <div class="brand-logo"></div>
                <p class="korkortsjakten-ab-c hidden-sm-up">
                    Körkortsjakten AB<br>Slottsgatan 27<br>722 11 Västerås<br>Org.nr 556569-2125
                </p>
                <div class="kontakt-korkortsjakt d-flex flex-column hidden-sm-down text-center">
                    <a class="contact-link" href="mailto:kontakt@korkortsjakten.se"><b>kontakt@korkortsjakten.se</b></a>
                    <span>Kundtjänst måndag-fredag 9.30-16.30</span>
                    <span>Lunch stängt 13.15-14.00</span>
                    <span>{{ config('services.phones.customers.text') }}</span>
                    <a class="vagen-till-korkort-m text-decoration-underline" href="{{ url('/faq') }}">Vanliga frågor - FAQ</a>
                </div>
            </div>
            <div class="col-xs-12 col-md-4 order-md-3 order-1 mb-2 px-0">
                <div class="kontakt-korkortsjakt d-flex flex-column text-xs-center hidden-sm-up">
                    <a class="contact-link" href="mailto:kontakt@korkortsjakten.se"><b>kontakt@korkortsjakten.se</b></a>
                    <span>Kundtjänst måndag-fredag 9.30-16.30</span>
                    <span>Lunch stängt 13.15-14.00</span>
                    <span>{{ config('services.phones.customers.text') }}</span>
                    <a class="vagen-till-korkort-m text-decoration-underline" href="{{ url('/faq') }}">Vanliga frågor - FAQ</a>
                </div>
                <p class="korkortsjakten-ab-c hidden-sm-down text-md-right">
                    Körkortsjakten AB<br>Slottsgatan 27<br>722 11 Västerås<br>Org.nr 556569-2125
                </p>
            </div>
            <div class="road road-up">
                <img src="/build/img/road-right.png">
            </div>
        </div>
    </div>
</footer>
