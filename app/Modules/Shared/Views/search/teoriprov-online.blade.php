@extends('shared::layouts.default')
@section('pageTitle', 'Körkortsteori och Testprov |')
@section('content')
    <div class="search-page">
        <div class="search-filters">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-6 offset-md-3">
                        <!-- Header1 -->
                        <div class="row text-center">
                            <div class="col-lg-12 header1">
                                <h1>Körkortsteori och Testprov</h1>
                            </div>
                        </div>

                        <!-- Box -->
                        <div class="row d-flex justify-content-center mt-2">
                            <div class="teori-box">
                                <div class="teori-image"></div>
                                <div class="teory-content">
                                    <div class="text-center" style="margin-top: 40px;">
                                        <span class="teori-box-header">Giltig i 6 månader</span>
                                    </div>
                                    <div class="reel-box">
                                        <div class="d-flex box-row">
                                            <img class="reel-img ml" src="{{asset('build/img/reel-white.png')}}"
                                                 alt="Reel">
                                            <span class="reel-text">Alla 1300 körkortsfrågor</span>
                                        </div>
                                        <div class="d-flex box-row">
                                            <img class="reel-img" src="{{asset('build/img/reel-white.png')}}"
                                                 alt="Reel">
                                            <span class="reel-text">Hela körkortsresan</span>
                                        </div>
                                        <div class="d-flex box-row">
                                            <img class="reel-img" src="{{asset('build/img/reel-white.png')}}"
                                                 alt="Reel">
                                            <span class="reel-text">Obegränsat antal prov</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center"
                                         style="margin-top: 45px; margin-bottom: 45px;">
                                        <a class="btn btn-success"
                                           href="{{route('shared::payment.index') . '?skola='.$school->id.'&kurser='.$course->id.'&'.$course->id.'=1'}}">
                                            Köp 399 kr
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row teori-description">
                            <div class="col-lg-12">
                                <span class="teori-description-text">Med tillgång till alla körkortsfrågor du behöver kunna för att klara teoriprovet. Alla körkortsfrågor har en förklaring som snabbt hjälper dig att förstå även det svåraste frågorna. Studera och testa dig när du vill, obegränsat antal prov. För att göra körkortsteorin lättare att förstå kan du få allt innehåll uppläst för dig. Körkortsfrågorna och proven fungerar på alla enheter, både dator och mobil. iKörkort har efter flera års erfarenhet skapat marknadens bästa innehåll! Dessutom billigt och smidigt, Skippa dyra teorilektioner och tråkiga böcker och plugga på dina villkor istället – när du har lust, var du än är! Samtliga frågor och prov finns på svenska alternativt engelska.</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
