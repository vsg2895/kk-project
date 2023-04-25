@extends('shared::layouts.default')
@section('robots')
    <meta name="robots" content="noindex">
@stop
@section('pageTitle')
    Musikhjalpen |
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

    .icon-img {
        height: 20px;
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
    <div class="container-fluid">
        <div class="header-block card card-block">
            <div class="col-12 col-md-3 d-flex justify-content-center align-items-center">
                <img src="{{asset('images/landing/musikhjalpen.png')}}" alt="musikhjalpen">
            </div>
            <div class="col-12 col-md-6 text-center d-flex flex-column justify-content-center">
                <h2><b>Stöd Musikhjälpen tillsammans med Körkortsjakten
                    - För en tryggare barndom på flykt från krig.</b>
                </h2>
                <h2><b>Var även med och tävla om presentkort värde 10 000:-</b></h2>
            </div>
            <div class="col-12 col-md-3 d-flex justify-content-center align-items-center">
                <img src="{{asset('images/landing/jultavling.png')}}" alt="jultavling">
            </div>
        </div>

        <div class="row card card-block align-items-center justify-content-between">
            <div class="col-12 col-md-8 d-flex flex-column">
                <h3><b>Hjälp oss stödja Musikhjälpen</b></h3>
                <p>Gör som oss och många andra, och skänk en slant till Musikhjälpen. I år skramlar vi på Körkortsjakten för en tryggare barndom på flykt från krig – för att vi kan,
                    vi vill och vi bryr oss! Klicka nedan för att vara med och bidra till en bättre och säkrare värld!
                </p>
                <a href="https://bossan.musikhjalpen.se/koerkortsjakten-hjaelp-oss-stoedja-musikhjaelpen" class="btn btn-success ga-med-idag">TILL BÖSSAN</a>
                <br>
                <p>
                    <b>Tävlingsinformation:</b>
                </p>
                <p>
                    <img class="icon-img" src="{{asset('images/icons/snowman.png')}}" alt="snowman">
                    <img class="icon-img" src="{{asset('images/icons/christmas-tree.png')}}" alt="christmas-tree">
                    <img class="icon-img" src="{{asset('images/icons/gift.png')}}" alt="gift">
                    JULTÄVLING - 3 lyckliga vinnare
                    <img class="icon-img" src="{{asset('images/icons/snowman.png')}}" alt="snowman">
                    <img class="icon-img" src="{{asset('images/icons/christmas-tree.png')}}" alt="christmas-tree">
                    <img class="icon-img" src="{{asset('images/icons/gift.png')}}" alt="gift">
                </p>
                <p>Ta chansen och vinn presentkort <img class="icon-img" src="{{asset('images/icons/salute.png')}}" alt="salute"></p>

                <p style="margin-bottom: 0">1.a pris: Presentkort á 5 000:-</p>
                <p style="margin-bottom: 0">2.a pris: Presentkort á 3 000:-</p>
                <p>3.a pris: Presentkort á 2 000:-</p>

                <p style="margin-bottom: 0">För att delta och ha chans att vinna så behöver du:</p>
                <ul style="list-style: none">
                    <li><img class="icon-img" src="{{asset('images/icons/one.png')}}" alt="one">Följa @korkortsjakten på Instagram</li>
                    <li><img class="icon-img" src="{{asset('images/icons/two.png')}}" alt="two">Gilla detta inlägg</li>
                    <li><img class="icon-img" src="{{asset('images/icons/three.png')}}" alt="three">Tagga 3 vänner <img class="icon-img" src="{{asset('images/icons/achqov-anel.png')}}" alt="achqov-anel"></li>
                </ul>

                <p>Hjälp oss att stödja Musikhjälpen - För en tryggare barndom på flykt från krig <br>{{--                icon missing--}}
                    (För extra chans att vinna, bidra till vår Körkortsjakten-bössa på Musikhjälpen)</p>

                <p>Tävlingen pågår endast på vår Instagram-sida till och med tisdag den 27 december 2022. Tre vinnare kommer slumpmässigt att lottas fram och presenteras 28 december.</p>

                <p>Lycka till!<img class="icon-img" src="{{asset('images/icons/fifa-cup.png')}}" alt="fifa-cup"></p>

                <p>OBS, Musikhjälpen / Radiohjälpen har ingenting med själva jultävlingen att göra med och frågor gällande detta besvaras av Körkortsjakten.</p>

                <a href="https://www.instagram.com/p/CmG3cwxDdpu/?igshid=YmMyMTA2M2Y=" class="btn btn-success ga-med-idag">TILL TÄVLING</a>
            </div>
            <div class="col-12 col-md-3 d-flex flex-column justify-content-center align-items-center">
                <img src="{{asset('images/landing/temabild.png')}}" alt="temabild">
                <img src="{{asset('images/landing/temabudskap.png')}}" alt="temabild">
            </div>
<!--            <div class="col-12 col-md-3 d-flex flex-column justify-content-center">
                <img src="{{asset('images/landing/temabild.png')}}" alt="temabild">
            </div>
            <div class="col-12 col-md-3 d-flex flex-column justify-content-center">
                <img src="{{asset('images/landing/temabudskap.png')}}" alt="temabild">
            </div>-->
        </div>
    </div>
@endsection
