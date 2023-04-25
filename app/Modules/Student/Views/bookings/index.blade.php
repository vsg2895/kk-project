@extends('student::layouts.default')

<style>

    .grid {
        height: 20%;
        max-width: 100%;
        width: 100%;
        background: radial-gradient(ellipse at center, rgba(236, 240, 241, 1) 0%, rgba(25, 25, 25, 1) 100%), url("https://www.transparenttextures.com/patterns/egg-shell.png");
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        margin: 0;
        border: .125em solid #ECF0F1;
    }

    .grid-item {
        width: 18%;
        height: 90%;
        background-color: transparent;
        margin: auto;
        border: .125em solid #181818;
        text-align: center;
        position: relative;
    }

    .grid-item-interior {
        height: 100%;
        width: 100%;
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        margin: 0 auto;
        background-color: #FFFFFF;
        background-image: url("https://www.transparenttextures.com/patterns/lined-paper.png");
        box-shadow: inset 0 0 20px 0 rgba(41, 41, 41, 0.4);
    }

    .grid-item-interior span.benefits {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: auto;
        margin-top: 6.5%;
        color: #292929;
        font-size: 1.5em;
        font-weight: 500;
    }

    .grid-item-interior span.benefits .course-name {
        top: 0;
        left: 0;
        width: 100%;
        height: auto;
        margin-top: 6.5%;
        color: #000000;
        font-size: 1.2em;
        font-weight: 400;
    }

    .grid-item-interior span.benefits .benefit-info {
        font-weight: 600;
    }

    .door-large span.course-name {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: auto;
        margin-top: 6.5%;
        color: #c9c3c3;
        font-size: 1.2em;
        font-weight: 600;
    }

    .door {
        width: 100%;
        height: 100%;
        display: block;
        overflow: hidden;
        position: relative;
        top: 0;
        left: 0;
    }

    .door-large {
        height: 100%;
        width: 100%;
        display: block;
        position: absolute;
        transition: all 0.5s ease-in-out, border 0.1s ease;
        background: #292929 url('../../../../images/garage.png') no-repeat center center;
        border: .0625em solid #181818;
        cursor: pointer;
    }

    .door2.active .door-large {
        transform: translateY(-100%);
    }

    @media (max-width: 621px) {
        .grid {
            height: 100%;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            flex-wrap: unset;
        }

        .grid-item {
            width: 100%;
            height: 100%;
            margin-top: 5px;
        }
    }
</style>

@section('content')
    {{--loyalty program section--}}
    <header class="section-header student-section">
        <h1 class="page-title">Lojalitetsprogram – Din körresa mot körkortet</h1>
        <p class="">
            Här kan du följa din resa mot ditt körkort. Introduktionskursen är för dig som vill börja övningsköra
            privat,
            därefter har Ni Risk1 + Risk2 (Halkan) som är obligatoriska kurser. Teori material/undervisning behöver ni
            för att klara teoriprovet,
            sedan rekommenderas det även att ni tar ett par körlektioner via en trafikskola för att säkra er uppkörning.
            Vid varje avslutad och godkänd kurs öppnas garaget med nya förmåner/bonusar som appliceras automatiskt på
            ert konto på Körkortsjakten
            som ni kan ta del av vid nästa köp på valfri trafikskola. Saldot appliceras automatiskt vid köpet i kassan.
        </p>
    </header>

    <div class="card card-block mx-0">
        <div class="grid">
            @foreach($loyaltyData as $benefit)
                @if($benefit['open'])
                    <div class="grid-item">
                        <div class="door door2 open">
                            <div class="grid-item-interior">
                                <span class="benefits">
                                    <p><span class="course-name">{{$benefit['name']}}</span></p>
                                    @if($benefit['balance'])
                                        <p class="benefit-info"> Bonus Saldo: +{{$benefit['balance']}} kr</p>
                                    @endif
                                    @if($benefit['discount'])
                                        <p class="benefit-info"> Rabatt: {{$benefit['discount']}}% på Teori</p>
                                    @endif
                                </span>
                            </div>
                            <div class="door-large">
                                <span class="course-name">{{$benefit['name']}}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="grid-item">
                        <div class="door door2">
                            <div class="grid-item-interior">
                                <span> No Info Here </span>
                            </div>
                            <a href="{{$benefit['url'] ?? ''}}" target="_blank">
                                <div class="door-large">
                                    <span class="course-name">{{$benefit['name']}}</span>
                                </div>
                            </a>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    {{--bookings section--}}
    <header class="section-header student-section">
        <h1 class="page-title">Mina kurser</h1>
        <p class="">Du administrerar och avbokar dina kurser under Beställningar i menyraden till vänster.</p>
    </header>

    <div class="card card-block mx-0">
        @if($bookings->count())
            <div class="table-head table-row hidden-sm-down">
                <div class="table-cell col-md-5">
                    Kurs
                </div>
                <div class="table-cell col-md-3">
                    Deltagare
                </div>
                <div class="table-cell col-md-3">
                    Datum
                </div>
            </div>
            <div class="table">
                @foreach($bookings as $booking)

                    @if(!$booking->participant)
                        @continue
                    @endif

                    <div class="table-row">
                        <div class="table-cell col-md-5">
                            <a href="{{ route('student::bookings.show', ['id' => $booking->id]) }}">{{ $booking->course->name }}</a>
                            hos {{ $booking->course->school->name }}
                        </div>
                        <div class="table-cell col-md-3">

                            {{ $booking->participant->name }}
                        </div>
                        <div class="table-cell col-md-3">
                            {{ $booking->course->start_time->formatLocalized('%H:%M, %Y-%m-%d') }}
                        </div>
                        <div class="table-cell hidden-md-up more-button">
                            <a class="btn btn-sm btn-outline-primary"
                               href="{{ route('student::bookings.show', ['id' => $booking->id]) }}">Visa</a>
                        </div>
                        <div class="table-cell col-md-1 text-md-center">

                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <no-results title="Du har inte bokat några kurser än">
                <a class="btn btn-primary btn-primary" href="{{ route('shared::introduktionskurs') }}"
                   slot="description">Sök efter lediga kurser</a>
            </no-results>
        @endif
    </div>
@endsection

@section('no-vue')
    <script>
        $(document).ready(function () {
            $(".door.open").click(function () {
                $(this).toggleClass("active");
            });
            setTimeout(function () {
                $(".door.open").click();
            }, 500)
        });
    </script>
@endsection
