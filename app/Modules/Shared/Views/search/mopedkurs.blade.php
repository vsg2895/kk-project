@extends('shared::layouts.default')
@section('pageTitle', 'Mopedkurs - Boka mopedkur idag |')
@section('content')
    <div class="search-page">
        <div class="search-filters">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 header1">
                        <h1>Mopedkurs AM</h1>
                    </div>
                </div>
                <div class="row margin-top10px">
                    <div class="col-lg-12">
                        <h2>Boka mopedkurs och betala online med Klarna</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <h6>Hitta och boka mopedkurs hos Körkortsjakten</h6>
                    </div>
                </div>
                <div class="row">
                    <!-- City selector -->
                    <div class="col-xs-8 col-lg-4">
                        <div id="landing-search-city" class="col">
                            <semantic-dropdown :search="true" id="cities" placeholder="Sök stad" form-name="city_id" :data="{{ $cities }}">
                                <template slot="dropdown-item" slot-scope="props">
                                    <div class="item" :data-value="props.item.slug">
                                        <div class="item-text">@{{ props.item.name }}</div>
                                    </div>
                                </template>
                            </semantic-dropdown>
                        </div>
                    </div>
                    <div class="float-right">
                        <a href="{{ route('shared::mopedkurs') }}" class="btn btn-primary button-find hitta">Hitta</a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="search-links-block">
                            <a href="/mopedkurs/all" class="search-links">All</a>
                            @include('shared::components.course_type.links', ['slug' => 'mopedkurs'])

                        </div>
                    </div>
                </div>

                <div class="row margin-top15px">
                    <div class="col-lg-6">

                        <h3>Körkort för moppen (AM)</h3>
                        <p align="justify">
                            Om du har körkortsbehörighet för AM ger det dig rätt att köra moped klass I,
                            också kallad EU-moped som är ett fordon konstruerad för en hastighet på
                            45km/h.
                        </p>

                        <h3>Krav</h3>
                        <p align="justify">De kraven som man behöver uppfylla för att få ta AM-kort är dessa:</p>
                        <ul>
                            <li>Du måste ansökt och fått ett godkänt körkortstillstånd.</li>
                            <li>Vara permanent bosatt i Sverige eller har studerat här i minst sex månader.</li>
                            <li>Gått en utbildning hos en behörig utbildare.</li>
                            <li>Fyllt 15 år.</li>
                            <li>Gjort ett kunskapsprov och blivit godkänd.</li>
                            <li>Du har inget körkort som är utfärdat i någon annan stat inom
                                EES-området. Ett sådant körkort går däremot att byta ut mot ett svensk.
                            </li>
                        </ul>

                        <h3>Körkortstillstånd</h3>
                        <p align="justify">
                            För att kunna få övningsköra samt göra förarprovet så behöver man ha ett
                            giltigt körkortstillstånd. Vid ansökan så kontrollerar Transportstyrelsen att de
                            kraven som de ställer uppfylls.
                        </p>
                        <p align="justify">
                            <a href="https://etjanst.transportstyrelsen.se/extweb/kktillstgrI" class="btn btn-md btn-primary" target="_blank">
                                Ansök om tillstånd
                            </a>
                        </p>

                        <h3>Utbildningen</h3>
                        <p align="justify">
                            När man gör sin utbildning så är det viktigt att man kommer ihåg att teori och
                            praktik är lika viktiga. När du läser teori så lär du dig att köra med gott
                            omdöme och du lär dig att klara av de spelregler som finns i trafiken.
                            Utbildningen är minst 12 timmar lång och genomförs hos en behörig utbildare.
                            Minst fyra av dessa timmar är då praktisk träning och resten av tiden är
                            teoretisk.
                        </p>

                        <h4>Övningsköra</h4>
                        <p align="justify">
                            Kraven för att man ska få kunna övningsköra är att du minst måste vara 14år
                            och 9 månader. Du måste ha ett godkänt körkortstillstånd och du måste vara
                            inskriven hos en utbildare där du också kan övningsköra. Det är nämligen inte tillåtet
                            att övningsköra privat.
                            När den som utbildar dig sedan rapporterar att du har genomgått utbildningen
                            så bokar antingen den som utbildat dig eller du själv en tid kunskapsprov.
                            Efter att du gjort din AM utbildning så är.
                        </p>

                        <h4>Kunskapsprov</h4>
                        <p align="justify">
                            Under ditt kunskapsprov så ska du visa att du besitter de kunskaper som
                            behövs, så att du kan köra med gott omdöme i trafiken. Kunskapsprovet är det enda
                            provet du gör för att få ditt AM kort.
                        </p>
                        <p align="justify">
                            Avgiften för ett kunskapsprov är 325 kr. På vardagar efter kl 18.00 och på
                            helger är avgiften 400 kr.
                        </p>
                        <p align="justify">
                            <a href="https://www.trafikverket.se/Privat/Korkortsprov/" class="btn btn-md btn-primary" target="_blank">
                                Boka ditt kunskapsprov
                            </a>
                        </p>

                        <h4>Anpassade prov</h4>
                        <p align="justify">
                            Om du har svårigheter med att läsa eller skriva, eller om du inte förstår
                            svenska så bra, så finns det anpassade kunskapsprov. Du kan få utföra ett muntligt
                            prov, ett prov som har längre provtid eller prov med teckenspråk eller med en
                            tolk. För att bli godkänd så behöver man få rätt på minst 52 av de 65
                            frågorna.
                        </p>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
