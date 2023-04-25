@extends('student::layouts.default') @section('content')
<header class="section-header student-section">
    <h1 class="page-title">Mina presentkort</h1>
    <p class="">Här ser du dina presentkort och hur mycket du har kvar i din pott</p>
</header>

@include('shared::components.errors') @include('shared::components.message')

<div class="card card-block mx-0 reset-card-block-padding">
    @if($giftCards->count())
    <div class="table-head table-row hidden-sm-down">
        <div class="table-cell col-md-6">
            Belopp
        </div>
        <div class="table-cell col-md-6">
            Giltighetstid
        </div>
    </div>
    <div class="table">
        @foreach($giftCards as $giftCard)
        <div class="table-row">
            <div class="table-cell col-md-6">
                <span class="link hidden-md-up">Belopp : </span>
                {{ $giftCard->remaining_balance }} kr
            </div>
            <div class="table-cell col-md-6">
                <span class="link hidden-md-up">Giltighetstid :</span>
                {{ Carbon\Carbon::parse($giftCard->expires)->toDateString() }}
            </div>
        </div>
        @endforeach
    </div>
    @else
   <div style="margin-bottom: 30px;" >
        <no-results title="Du har inga presentkort kopplade.">
        </no-results>
   </div>
    @endif

    <div class="col-lg-12 col-sm-12">
        <div class="col-sm-6 mb-1">
            <span class="h2" >Total belopp:</span>
            <span class="h2 text-success">{{ $user->giftCardBalance }} kr</span>
        </div>
        <div class="col-sm-6">
            <form class="float-sm-right" action="{{ route('student::giftcard.claim') }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="align-right">
                    <div class="input-group">
                        <input type="text" class="form-control simple-input" id="token" name="token" placeholder="Ange presentkortskod">
                        <span class="input-group-btn">
                            <button class="btn btn-success" submit="button">Lägg till</button>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<footer class="section-header">
    <h1 class="page-title">Hur fungerar det?</h1>
    <p class="">
        Klistra i din presentkortskod ovan och tryck lägg till för att aktivera ditt saldo.  Efter det är du redo att boka och betala
        kurser. Ditt saldo fungerar som betalmedel på alla trafikskolor som är anslutna till körkortsjaktens presentkort
        och har en symbol med ett paket vid trafikskolans namn. Vid betalning anger du om du vill betala med ditt presentkort,
        Klarna eller både och.
    </p>
    <p>Observera att köp från ditt presentkort endast kan ske via körkortsjakten.se </p>
</footer>
@endsection
