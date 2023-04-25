@extends('shared::layouts.default')
@section('robots')
    <meta name="robots" content="noindex">
@stop
@section('pageTitle')
    Top Partner |
@stop
<style>
    .top-partner-medal-header {
        color: #edd33d;
        font-size: 7rem;
    }

    .top-partner-size {
        font-size: 3rem;
    }

    .top-partner-icon-in-block {
        color: #edd33d;
        font-size: 4rem;
    }

    .card-top-partner {
        flex: 1 0 350px;
        display: flex;
        border-radius: 5px;
        border: none;
        box-shadow: 0 1px 4px 0 rgb(0 0 0 / 15%);
        min-height: 18rem !important;
        margin-top: 15px;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 0 30px;
        background: #fff;
        padding-top: 30px;
    }

    .card-top-partner p {
        height: 35%;
    }

    .card-top-partner i {
        height: 30%;
    }

    .card-top-partner h3 {
        height: 5%;
    }

    .card-top-partner.middle {
        margin-left: 15px;
        margin-right: 15px;
    }

    .card-top-partner-container {
        display: flex;
        align-items: stretch;
        justify-content: space-between;
        margin: 15px;
        flex-wrap: wrap;
    }

    .top-partner-logo-div {
        min-height: 300px !important;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .bold-desc {
        font-weight: 1000;
    }

    .ga-med-idag {
        max-width: fit-content !important;
    }

    .header-block {
        display: flex !important;
        flex-wrap: wrap;
    }

    @media only screen and (max-width: 1024px) {
        .card-top-partner.middle {
            margin-right: 0px;
        }
    }

    @media only screen and (max-width: 768px) {
        .card-top-partner.middle {
            margin-left: 0;
        }

        .header-block {
            justify-content: center;
            flex-direction: column-reverse;
        }
    }

    @media only screen and (max-width: 370px) {
        .top-partner-size {
            font-size: 2rem;
        }
    }

    @media only screen and (max-width: 400px) {
        .card-top-partner {
            flex: unset;
        }

        .card-top-partner i {
            height: 20%;
        }
    }
</style>
@section('content')

    @include('shared::components.message')
    @include('shared::components.errors')
    <div class="container-fluid">
        <div class="header-block card card-block">
            <div class="col-12 col-md-6 d-flex flex-column">
                <h2>Top Partner för
                    rekommenderade
                    samarbetspartners</h2>
                <h4 class="bold-desc">Få upp till 35% fler
                    bokningar som medlem.</h4>
                <br>

                <h4>Vad är Top Partner?</h4>
                <p>Programmet är ett unikt tillfälle för våra bästa partners att synas
                    mer men även ett kvitto på att kriterierna för att kunna tilldelas
                    kvalitetsstämpeln har uppfyllts. Mot en liten höjning av provisionen
                    på 2,5% så kan Körkortsjaktens Top Partners förvänta sig en enorm
                    ökning av antalet bokningar.
                </p>
                <br>
                <a href="#top_partner_form" class="btn btn-success ga-med-idag">Gå med idag!</a>
                <br>
                <h4>Så vad händer när ni blir Top Partner?</h4>
                <p>En tydligare synlighet för våra partners med en permanent topplacering i sökresultaten samt att ni får en Top Partnersymbol.
                </p>
            </div>
            <div class="col-12 col-md-6 d-flex justify-content-center align-items-center  top-partner-logo-div">
                <i class="fa fa-medal top-partner-medal-header"></i>
                <p class="top-partner-size">Top Partner</p>
            </div>
        </div>
        <div class="card-top-partner-container">
            <div
                class="card-top-partner">
                <i class="fa fa-medal top-partner-icon-in-block"></i>
                <h3 class="mt-1">Kvalitetsstämpel</h3>
                <p class="mt-1 mb-2"><strong>Er trafikskola får en Top Partnersymbol som visar att ni är
                        kvalitetsstämplade utifrån en enastående kundupplevelse tack vare
                        den utmärka service och utbud som erbjuds.</strong></p>
            </div>
            <div
                class="card-top-partner middle">
                <i class="fas fa-eye top-partner-icon-in-block"></i>
                <h3 class="mt-1">Fler sidvisningar</h3>
                <p class="mt-1 mb-2"><strong>Förutom symbolen för ni även ökad synlighet i sökresultaten vilket
                        får en markant ökning i sidvisningar - upp till 65% mer!</strong></p>
            </div>
            <div
                class="card-top-partner">
                <i class="fas fa-dollar-sign top-partner-icon-in-block"></i>
                <h3 class="mt-1">Fler kunder</h3>
                <p class="mt-1 mb-2"><strong>Synlighet = fler gäster! Allt handlar om synlighet och som Top
                        Partner får ni upp till 35% fler bokningar!</strong></p>
            </div>
        </div>

        <div class="row card card-block align-items-center justify-content-between">
            <h3>+ För att sticka ut ytterligare så kommer även sökfältet
                för trafikskolan som Top Partner att se annorlunda ut än
                den traditionella vyn. Som partner får ni även ta del av
                unika utskick mot vår kundbas och andra
                annonseringskanaler för ökad exponering m.m.
            </h3>
        </div>
        <div class="row card card-block align-items-center justify-content-between">
            <div class="page-footer-text col-12 col-xl-5 col-lg-5  d-flex flex-column">
                <br>
                <br>
                <h3>Hur blir man Top Partner?</h3>
                <p>Detta medlemskap är begränsat. Endast de främsta 30% av våra
                    partners kan gå med, och ni är en av dem!</p>
                <br>
                <p>För att kunna försäkra oss om att våra partners håller hög kvalitet
                    och standard så måste kriterier uppfyllas för kvalifikation av
                    medlemskap.</p>
                <br>
                <p>Status för Top Partnerkvalifikation:
                    Kundbetyg 4/5</p>
            </div>
            <div class=" col-12 col-xl-6 col-lg-6  d-flex justify-content-center">
                <form method="POST" id="top_partner_form" class="w-100 d-flex flex-column justify-content-center"
                      action="{{ route('shared::page.application-top-partner') }}">
                    {{ csrf_field() }}
                    <div class="jumbotron jumbotron-light"><h3>Gå med i Top Partner</h3>
                        <div class="form-group "><label for="application-top-school-name">Trafikskolans namn</label>
                            <input id="application-top-school-name"
                                   type="text"
                                   name="school_name"
                                   class="form-control simple-input">
                        </div>
                        <div class="form-group "><label for="application-top-school-email">E-post</label>
                            <input id="application-top-school-email"
                                   name="school_email"
                                   type="email"
                                   class="form-control simple-input">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Skicka</button>
                </form>
            </div>
        </div>
    </div>
@endsection
