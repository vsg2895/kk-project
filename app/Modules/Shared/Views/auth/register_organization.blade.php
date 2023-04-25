@extends('shared::layouts.default')
@section('pageTitle', 'Registrera skola -')
@section('body-class', 'page-auth little-margin')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-lg-8 offset-lg-2 mb-2">
                <div class="youtube-video">
                    <iframe width="560" height="315"
                            src="https://www.youtube.com/embed/CwipLTQEs_A?modestbranding=1&rel=0&ytp-pause-overlay=0"
                            frameborder="0" allowfullscreen></iframe>
                </div>
                <h1>
                    Inga fasta avgifter, ingen bindningstid, endast en liten provision på de extra intäkter ni får av
                    oss
                </h1>

            </div>
            <div class="col-sm-12 text-center">
                <h2>Anslut er trafikskola</h2>
            </div>
            <div class="col-md-4">
                <div class="mb-1">
                    <h4>Behöver ni hjälp eller har en fråga?</h4>
                    <div>
                        <span>Ring oss på</span>
                        <a href="tel:{{ config('services.phones.customers.regular') }}">{{ config('services.phones.customers.text') }}</a>
                    </div>
                </div>
                <div class="usps hidden-sm-down">
                    <h3>Enkelt</h3>
                    <p>”One stop shop” för körkort</p>

                    <h3>Säker betalning</h3>
                    <p>Ni är garanterad full betalning för alla bokningar</p>

                    <h3>Smart administrering</h3>
                    <p>Hantera kurser och erbjudanden</p>

                    <h3>Onlinestatistik</h3>
                    <p>Följ er utveckling och läs av trender</p>

                    <h3>Marknadsföring</h3>
                    <p>Körkortsjakten erbjuder stor marknadsnärvaro i flera kanaler</p>

                    <h3>Omdömestjänst</h3>
                    <p>Era betyg syns på Trafikskolans sida</p>
                </div>
            </div>
            <div class="col-md-4">
                <div>
                    <form id="login-form" method="POST" class="usps"
                          action="{{ route('auth::register.organization.store') }}">
                        {{ csrf_field() }}
                        @if($schoolToClaim)
                            <input type="hidden" name="claim" value="{{ $schoolToClaim }}">
                        @endif
                        <h3>Om trafikskolan</h3>
                        <div class="form-group @if($errors->has('name')) has-error @endif">
                            <label class="label-hidden" for="name">Företagsnamn</label>
                            <span class="far fa-sticky-note form-control-icon"></span>
                            <input class="form-control @if($errors->has('name')) form-control-error @endif" placeholder="Namn på din organisation" type="text" id="name"
                                   name="name" value="{{ old('name') }}"/>
                            @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                            @endif
                        </div>

                        <div class="form-group @if($errors->has('org_number')) has-error @endif">
                            <label class="label-hidden" for="org_number">Organisationsnummer</label>
                            <span class="far fa-edit form-control-icon"></span>
                            <input class="form-control @if($errors->has('org_number')) form-control-error @endif" placeholder="Organisationsnummer" type="text" id="org_number"
                                   name="org_number" value="{{ old('org_number') }}"/>
                            @if($errors->has('org_number'))
                                <div class="invalid-feedback">{{ $errors->first('org_number') }}</div>
                            @endif
                        </div>

                        <h3>
                            Kontodetaljer<br>
                            <span class="text-sm">Ange personliga uppgifter för ditt användarkonto</span>
                        </h3>
                        <div class="form-group @if($errors->has('given_name')) has-error @endif">
                            <label class="label-hidden" for="given_name">Förnamn</label>
                            <span class="far fa-user form-control-icon"></span>
                            <input class="form-control @if($errors->has('given_name')) form-control-error @endif" placeholder="Förnamn" type="text" id="given_name"
                                   name="given_name" value="{{ old('given_name') }}"/>
                            @if($errors->has('given_name'))
                                <div class="invalid-feedback">{{ $errors->first('given_name') }}</div>
                            @endif
                        </div>

                        <div class="form-group @if($errors->has('family_name')) has-error @endif">
                            <label class="label-hidden" for="family_name">Efternamn</label>
                            <span class="far fa-user form-control-icon"></span>
                            <input class="form-control @if($errors->has('family_name')) form-control-error @endif" placeholder="Efternamn" type="text" id="family_name"
                                   name="family_name" value="{{ old('family_name') }}"/>
                            @if($errors->has('family_name'))
                                <div class="invalid-feedback">{{ $errors->first('family_name') }}</div>
                            @endif
                        </div>

                        <div class="form-group @if($errors->has('email')) has-error @endif">
                            <label class="label-hidden" for="email">E-post</label>
                            <span class="fa fa-envelope form-control-icon"></span>
                            <input class="form-control @if($errors->has('email')) form-control-error @endif" placeholder="Email" type="email" id="email" name="email"
                                   value="{{ old('email') }}"/>
                            @if($errors->has('email'))
                                <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                            @endif
                        </div>

                        <div class="form-group @if($errors->has('phone_number')) has-error @endif">
                            <label class="label-hidden" for="phone_number">Telefonnummer</label>
                            <span class="fa fa-phone form-control-icon"></span>
                            <input class="form-control @if($errors->has('phone_number')) form-control-error @endif " placeholder="Telefonnummer" type="text" id="phone_number"
                                   name="phone_number" value="{{ old('phone_number') }}"/>
                            @if($errors->has('phone_number'))
                                <div class="invalid-feedback">{{ $errors->first('phone_number') }}</div>
                            @endif
                        </div>

                        <h3>Lägg upp ny trafikskola</h3>

                        <div class="form-group @if($errors->has('school.name')) has-error @endif">
                            <label class="label-hidden" for="school_name">Namn på din trafikskola</label>
                            <span class="far fa-sticky-note form-control-icon"></span>
                            <input class="form-control @if($errors->has('school.name')) form-control-error @endif" placeholder="Namn på din trafikskola" type="text"
                                   id="school_name" name="school[name]" value="{{ old('school.name') }}"/>
                            @if($errors->has('school.name'))
                                <div class="invalid-feedback">{{ $errors->first('school.name') }}</div>
                            @endif
                        </div>

                        <div class="form-group @if($errors->has('school.phone_number')) has-error @endif">
                            <label class="label-hidden" for="phone_number">Telefonnummer</label>
                            <span class="fa fa-phone form-control-icon"></span>
                            <input class="form-control @if($errors->has('school.phone_number')) form-control-error @endif" placeholder="Telefonnummer" type="text" id="school_phone_number"
                                   name="school[phone_number]" value="{{ old('school.phone_number') }}"/>
                            @if($errors->has('school.phone_number'))
                                <div class="invalid-feedback">{{ $errors->first('school.phone_number') }}</div>
                            @endif
                        </div>

                        <div class="form-group @if($errors->has('school.contact_email')) has-error @endif mb-1">
                            <label class="label-hidden" for="email">E-post</label>
                            <span class="far fa-envelope form-control-icon"></span>
                            <input class="form-control @if($errors->has('school.contact_email')) form-control-error @endif" placeholder="Email" type="email" id="email"
                                   name="school[contact_email]" value="{{ old('school.contact_email') }}"/>
                            @if($errors->has('school.contact_email'))
                                <div class="invalid-feedback">{{ $errors->first('school.contact_email') }}</div>
                            @endif
                        </div>

                        <div id="landing-search-city" class="col clearfix">
                            <semantic-dropdown :search="true" :initial-item="{{ old('school.city_id', 0) ?: 0 }}" id="cities"
                                               placeholder="Sök stad" form-name="school[city_id]"
                                               :data="{{ $cities }}">
                                <template slot="dropdown-item" slot-scope="props">
                                    <div class="item" :data-value="props.item.id">
                                        <div class="item-text">@{{ props.item.name }}</div>
                                    </div>
                                </template>
                            </semantic-dropdown>
                            @if($errors->has('school.city_id'))
                                <div class="invalid-feedback">{{ $errors->first('school.city_id') }}</div>
                            @endif
                        </div>
                        <div style="height: 20px"></div>
                        <div class="form-check">
                            <label class="form-checkbox-wrapper">
                                <input type="checkbox" id="terms" name="terms" required>
                                <span class="checkmark"></span>
                                <div class="label-text">
                                    Genom att skapa ett konto accepterar du Körkortsjaktens <a href="{{ url('/villkor-trafikskola') }}">Avtalsvillkor</a>.
                                </div>
                            </label>
                            @if($errors->has('terms'))
                                <div class="invalid-feedback">{{ $errors->first('terms') }}</div>
                            @endif
                        </div>
                        <button class="btn btn-black" type="submit">Anslut</button>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <div class="hidden-sm-down">
                    <h3>Vad innebär det att vara ansluten till <a href="{{ env('APP_URL') }}">Körkortsjakten</a>?
                    </h3>

                    <p>Först och främst så är detta kostnadsfritt, dvs gratis. Det finns heller ingen bindningstid och ni kan när som helst välja att avsluta samarbetet.</p>

                    <p>Så nu till det viktiga…När ni ansluter till <a href="{{ env('APP_URL') }}">körkortsjakten.se</a>
                        blir ni en del av Sveriges största mötesplats för elever och trafikskolor.</p>

                    <p>Ni speglar ert utbud av kurser och paket på er sida hos oss och vi förmedlar elever och intäkter till er.</p>

                    <p>Klarnas betallösning ingår i vårt samarbete. Transaktionsavgiften är 1,8%.</p>

                    <p>När vi förmedlat en intäkt tar vi en provision på 15% för kurser och 5% för paket. Ni betalar ingenting förrän vi förmedlat intäkten.</p>

                    <p>Våra administratörer lägger upp era kurser och paket till försäljning på <a href="{{ env('APP_URL') }}">körkortsjakten.se</a>
                        och ni får e-postbekräftelse när bokning har skett.</p>

                    <a class="btn btn-sm btn-primary" href="{{ route('shared::page.top-partner') }}">
                        Bli Top Partner &nbsp;<i class="fa fa-medal top-partner-medal-header"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-12 col-lg-8 offset-lg-2 mb-2">
                <p class="mt-1"><span class="lead">Är du elev?</span><br><a
                            href="{{ route('auth::register.student') }}">Registrera dig <i>här</i></a></p>
            </div>
        </div>
    </div>
@endsection
