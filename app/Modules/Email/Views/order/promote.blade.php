@component('mail::message', ['email' => $toEmail])

@php
    $user = \Jakten\Models\User::query()->where('email', $toEmail)->first();
    $benefit = null;
    //todo:fix this will be present, if user will make new order with the same segment
    if (in_array($course->vehicle_segment_id, \Jakten\Services\StudentLoyaltyProgramService::APPLY_AFTER_COURSE)) {
        $benefit = \Jakten\Models\Benefit::query()->where('user_id', $user->id)
        ->where('vehicle_segment_id', $course->vehicle_segment_id)->first();
    }
@endphp

@if($benefit)
@if($benefit->vehicle_segment_id === \Jakten\Models\VehicleSegment::RISK_ONE_CAR)
<img src="{{asset('images/garages/riskettan.png')}}" alt="Riskettan">
@elseif($benefit->vehicle_segment_id === \Jakten\Models\VehicleSegment::RISK_TWO_CAR)
<img src="{{asset('images/garages/risktvaan.png')}}" alt="Risktvaan">
@elseif($benefit->vehicle_segment_id === \Jakten\Models\VehicleSegment::RISK_ONE_TWO_COMBO)
<img src="{{asset('images/garages/risk-combo.png')}}" alt="Risk-combo">
@elseif($benefit->vehicle_segment_id === \Jakten\Models\VehicleSegment::INTRODUKTIONSKURS)
<img src="{{asset('images/garages/intro.png')}}" alt="Introduction">
@endif
@endif
<br>
# Grattis!

<a href="{{ route('shared::schools.rate', ['schoolId' => $course->school_id, 'courseId' => $course->id]) }}/?rating=5&email={{ $toEmail }}">{{ $user ? $user->getNameAttribute() : '' }}</a>
Du har genomfört din kurs "{{ trans('vehicle_segments.' . strtolower($course->segment->name)) }}" utmärkt.
Du har härmed öppnat dörren för fler härliga förmåner i jakten på ditt framtida körkort.

@if($benefit)
@if($benefit->vehicle_segment_id === \Jakten\Models\VehicleSegment::RISK_ONE_CAR)
Ditt saldo är nu laddat med:
# 75kr
10% rabatt på <a href="{{route('shared::teoriprov-online')}}">Körkortsteori</a>
@component('mail::button', ['url' => route('auth::login')])
Mitt konto
@endcomponent
<p style="font-size: 10px">Logga in på Mitt konto för att se all din medlemsinformation, aktuella erbjudanden och annat spännande.</p>
@elseif($benefit->vehicle_segment_id === \Jakten\Models\VehicleSegment::RISK_TWO_CAR)
Ditt saldo är nu laddat med:
# 100kr
10% rabatt på <a href="{{route('shared::teoriprov-online')}}">Körkortsteori</a>
@component('mail::button', ['url' => route('auth::login')])
Mitt konto
@endcomponent
<p style="font-size: 10px">Logga in på Mitt konto för att se all din medlemsinformation, aktuella erbjudanden och annat spännande.</p>
@elseif($benefit->vehicle_segment_id === \Jakten\Models\VehicleSegment::RISK_ONE_TWO_COMBO)
Ditt saldo är nu laddat med:
# 175kr
10% rabatt på <a href="{{route('shared::teoriprov-online')}}">Körkortsteori</a>
@component('mail::button', ['url' => route('auth::login')])
Mitt konto
@endcomponent
<p style="font-size: 10px">Logga in på Mitt konto för att se all din medlemsinformation, aktuella erbjudanden och annat spännande.</p>
@elseif($benefit->vehicle_segment_id === \Jakten\Models\VehicleSegment::INTRODUKTIONSKURS)
Ditt saldo är nu laddat med:
# 50kr
@component('mail::button', ['url' => route('auth::login')])
Mitt konto
@endcomponent
<p style="font-size: 10px">Logga in på Mitt konto för att se all din medlemsinformation, aktuella erbjudanden och annat spännande.</p>
@endif
@endif

---

@if($benefit)
@if($benefit->vehicle_segment_id === \Jakten\Models\VehicleSegment::RISK_ONE_CAR)
# Nästa steg

Börjar det bli dags för de obligatoriska kurserna Riskettan och Risktvåan?
Sök kurs för din stad och hitta bästa lediga tid redan idag!
@component('mail::button', ['url' => route('shared::risktvaan')])
Till Risk2
@endcomponent
@component('mail::button', ['url' => route('shared::teorilektion.paket')])
Till Körkortspaket
@endcomponent
<img src="{{asset('images/shoppa-nu.jpg')}}" alt="shoppa-nu">
---
@elseif($benefit->vehicle_segment_id === \Jakten\Models\VehicleSegment::RISK_TWO_CAR)
# Nästa steg

Börjar det bli dags för den obligatoriska kursen Risktvåan eller kanske lärarledda körlektioner?
Sök kurs för din stad och hitta bästa lediga tid redan idag!
@component('mail::button', ['url' => route('shared::teorilektion.paket')])
Till Körkortspaket
@endcomponent
@component('mail::button', ['url' => route('shared::teoriprov-online')])
Till Körkortsteori
@endcomponent
<img src="{{asset('images/shoppa-nu.jpg')}}" alt="shoppa-nu">
---
@elseif($benefit->vehicle_segment_id === \Jakten\Models\VehicleSegment::RISK_ONE_TWO_COMBO)
# Nästa steg

Börjar det bli dags för att plugga på Teorin eller kanske lärarledda körlektioner?
@component('mail::button', ['url' => route('shared::teorilektion.paket')])
Till Körkortspaket
@endcomponent
@component('mail::button', ['url' => route('shared::teoriprov-online')])
Till Körkortsteori
@endcomponent
<img src="{{asset('images/shoppa-nu.jpg')}}" alt="shoppa-nu">
---
@elseif($benefit->vehicle_segment_id === \Jakten\Models\VehicleSegment::INTRODUKTIONSKURS)
# Nästa steg

Börjar det bli dags för de obligatoriska kurserna Riskettan och Risktvåan?
Sök kurs för din stad och hitta bästa lediga tid redan idag!
@component('mail::button', ['url' => route('shared::riskettan')])
Till Risk1
@endcomponent
@component('mail::button', ['url' => route('shared::risktvaan')])
Till Risk2
@endcomponent
<img src="{{asset('images/shoppa-nu.jpg')}}" alt="shoppa-nu">
---
@endif
@endif

<style type="text/css">
    /* Normal email CSS */
    @-ms-viewport {
        width: device-width;
    }
    body {
        margin: 0;
        padding: 0;
        min-width: 100%;
    }
    table {
        border-collapse: collapse;
        border-spacing: 0;
    }
    td {
        vertical-align: top;
    }
    img {
        border: 0;
        -ms-interpolation-mode: bicubic;
        max-width: 100% !important;
        height: auto;
    }
    a {
        text-decoration: none;
        color: #119da2;
    }
    a:hover {
        text-decoration: underline;
    }

    *[class=main-wrapper],
    *[class=main-content]{
        min-width: 0 !important;
        width: 600px !important;
        margin: 0 auto !important;
    }
    *[class=rating] {
        unicode-bidi: bidi-override;
        direction: rtl;
    }
    *[class=rating] > *[class=star] {
        display: inline-block;
        position: relative;
        text-decoration: none;
    }

    @media (max-width: 621px) {
        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            -o-box-sizing: border-box;
        }
        table {
            min-width: 0 !important;
            width: 100% !important;
        }
        *[class=body-copy] {
            padding: 0 10px !important;
        }
        *[class=main-wrapper],
        *[class=main-content]{
            min-width: 0 !important;
            width: 320px !important;
            margin: 0 auto !important;
        }
        *[class=ms-sixhundred-table] {
            width: 100% !important;
            display: block !important;
            float: left !important;
            clear: both !important;
        }
        *[class=content-padding] {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }
        *[class=bottom-padding]{
            margin-bottom: 15px !important;
            font-size: 0 !important;
            line-height: 0 !important;
        }
        *[class=top-padding] {
            display: none !important;
        }
        *[class=hide-mobile] {
            display: none !important;
        }


        /* Prevent hover effects so double click issue doesn't happen on mobile devices */
        * [lang~="x-star-wrapper"]:hover *[lang~="x-star-number"]{
            color: #AEAEAE !important;
            border-color: #FFFFFF !important;
        }
        * [lang~="x-star-wrapper"]{
            pointer-events: none !important;
        }
        * [lang~="x-star-divbox"]{
            pointer-events: auto !important;
        }
        *[class=rating] *[class="star-wrapper"] a div:nth-child(2),
        *[class=rating] *[class="star-wrapper"]:hover a div:nth-child(2),
        *[class=rating] *[class="star-wrapper"] ~ *[class="star-wrapper"] a div:nth-child(2){
            display : none !important;
            width : 0 !important;
            height: 0 !important;
            overflow : hidden !important;
            float : left !important;
        }
        *[class=rating] *[class="star-wrapper"] a div:nth-child(1),
        *[class=rating] *[class="star-wrapper"]:hover a div:nth-child(1),
        *[class=rating] *[class="star-wrapper"] ~ *[class="star-wrapper"] a div:nth-child(1){
            display : block !important;
            width : auto !important;
            overflow : visible !important;
            float : none !important;
        }
    }
</style>
<table class="main-wrapper" style="border-collapse: collapse;border-spacing: 0;display: table;table-layout: fixed; margin: 0 auto; -webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100%;text-rendering: optimizeLegibility;background-color: #f5f5f5; width: 100%;">
    <tbody>
    <tr>
        <td style="padding: 0;vertical-align: top" class="">
            <div class="bottom-padding" style="margin-bottom: 0px; line-height: 30px; font-size: 30px;">&nbsp;</div>
        </td>
    </tr>
    <tr>
        <td style="padding: 0;vertical-align: top; width: 100%;" class="">
            <center>
                <!--[if gte mso 11]>
                <center>
                <table><tr><td class="ms-sixhundred-table" width="600">
                <![endif]-->

                <table class="main-content" style="width: 100%; max-width: 600px; border-collapse: separate;border-spacing: 0;margin-left: auto;margin-right: auto; border: 1px solid #EAEAEA; border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; background-color: #ffffff; overflow: hidden;" width="600">
                    <tbody>
                    <tr>
                        <td style="padding: 0;vertical-align: top;">
                            <table class="main-content" style="border-collapse: collapse;border-spacing: 0;margin-left: auto;margin-right: auto;width: 100%; max-width: 600px;">
                                <tbody>
                                <tr>
                                    <td style="padding: 0;vertical-align: top;text-align: left">
                                        <table class="contents" style="border-collapse: collapse;border-spacing: 0;width: 100%;">
                                            <tbody>
                                            <tr>
                                                <td class="content-padding" style="padding: 0;vertical-align: top">
                                                    <div style="margin-bottom: 0px; line-height: 30px; font-size: 30px;">&nbsp;</div>
                                                    <div class="body-copy" style="margin: 0;">

                                                        <div style="margin: 0;color: #60666d;font-size: 50px;font-family: sans-serif;line-height: 20px; text-align: left;">
                                                            <div class="bottom-padding" style="margin-bottom: 0px; line-height: 15px; font-size: 15px;">&nbsp;</div>
                                                            <div style="text-align: center; margin: 0; font-size: 10px;  text-transform: uppercase; letter-spacing: .5px;">Rating (select a star amount):</div>
                                                            <div class="bottom-padding" style="margin-bottom: 0px; line-height: 7px; font-size: 7px;">&nbsp;</div>
                                                            <div style="width: 100%; text-align: center; float: left;">
                                                                <div class="rating" style="text-align: center; margin: 0; font-size: 50px; width: 275px; margin: 0 auto; margin-top: 10px;">

                                                                    <table style="border-collapse: collapse;border-spacing: 0;width: 275px; margin: 0 auto; font-size: 50px; direction: rtl;" dir="rtl">
                                                                        <tbody><tr>
                                                                            <td style="padding: 0;vertical-align: top;" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                                                <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                                                                    <a href="{{ route('shared::schools.rate', ['schoolId' => $course->school_id, 'courseId' => $course->id]) }}/?rating=5&email={{ $toEmail }}" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="1">
                                                                                        <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                                                                        <div lang="x-full-star" style="margin: 0;display: inline-block; width:0; overflow:hidden;float:left; display:none; height: 0; max-height: 0;">★</div>
                                                                                    </a>
                                                                                    <a href="{{ route('shared::schools.rate', ['schoolId' => $course->school_id, 'courseId' => $course->id]) }}/?rating=5&email={{ $toEmail }}" class="star-number" target="_blank" lang="x-star-number" style="font-family: sans-serif;color: #AEAEAE; font-size: 14px; line-height: 14px; text-decoration: none; display: block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;border-bottom: 3px solid #FFFFFF; text-align: center;">5</a>
                                                                                </div>
                                                                            </td>
                                                                            <td style="padding: 0;vertical-align: top" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                                                <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                                                                    <a href="{{ route('shared::schools.rate', ['schoolId' => $course->school_id, 'courseId' => $course->id]) }}/?rating=4&email={{ $toEmail }}" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="2">
                                                                                        <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                                                                        <div lang="x-full-star" style="margin: 0;display: inline-block; width:0; overflow:hidden;float:left; display:none; height: 0; max-height: 0;">★</div>
                                                                                    </a>
                                                                                    <a href="{{ route('shared::schools.rate', ['schoolId' => $course->school_id, 'courseId' => $course->id]) }}/?rating=4&email={{ $toEmail }}" class="star-number" target="_blank" lang="x-star-number" style="font-family: sans-serif;color: #AEAEAE; font-size: 14px; line-height: 14px; text-decoration: none; display: block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;border-bottom: 3px solid #FFFFFF; text-align: center;">4</a>
                                                                                </div>
                                                                            </td>
                                                                            <td style="padding: 0;vertical-align: top" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                                                <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                                                                    <a href="{{ route('shared::schools.rate', ['schoolId' => $course->school_id, 'courseId' => $course->id]) }}/?rating=3&email={{ $toEmail }}" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="3">
                                                                                        <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                                                                        <div lang="x-full-star" style="margin: 0;display: inline-block; width:0; overflow:hidden;float:left; display:none; height: 0; max-height: 0;">★</div>
                                                                                    </a>
                                                                                    <a href="{{ route('shared::schools.rate', ['schoolId' => $course->school_id, 'courseId' => $course->id]) }}/?rating=3&email={{ $toEmail }}" class="star-number" target="_blank" lang="x-star-number" style="font-family: sans-serif;color: #AEAEAE; font-size: 14px; line-height: 14px; text-decoration: none; display: block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;border-bottom: 3px solid #FFFFFF; text-align: center;">3</a>
                                                                                </div>
                                                                            </td>
                                                                            <td style="padding: 0;vertical-align: top" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                                                <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                                                                    <a href="{{ route('shared::schools.rate', ['schoolId' => $course->school_id, 'courseId' => $course->id]) }}/?rating=2&email={{ $toEmail }}" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="4">
                                                                                        <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                                                                        <div lang="x-full-star" style="margin: 0;display: inline-block; width:0; overflow:hidden;float:left; display:none; height: 0; max-height: 0;">★</div>
                                                                                    </a>
                                                                                    <a href="{{ route('shared::schools.rate', ['schoolId' => $course->school_id, 'courseId' => $course->id]) }}/?rating=2&email={{ $toEmail }}" class="star-number" target="_blank" lang="x-star-number" style="font-family: sans-serif;color: #AEAEAE; font-size: 14px; line-height: 14px; text-decoration: none; display: block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;border-bottom: 3px solid #FFFFFF; text-align: center;">2</a>
                                                                                </div>
                                                                            </td>
                                                                            <td style="padding: 0;vertical-align: top" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                                                <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                                                                    <a href="{{ route('shared::schools.rate', ['schoolId' => $course->school_id, 'courseId' => $course->id]) }}/?rating=1&email={{ $toEmail }}" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="5">
                                                                                        <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                                                                        <div lang="x-full-star" style="margin: 0;display: inline-block; width:0; overflow:hidden;float:left; display:none; height: 0; max-height: 0;">★</div>
                                                                                    </a>
                                                                                    <a href="{{ route('shared::schools.rate', ['schoolId' => $course->school_id, 'courseId' => $course->id]) }}/?rating=1&email={{ $toEmail }}" class="star-number" target="_blank" lang="x-star-number" style="font-family: sans-serif;color: #AEAEAE; font-size: 14px; line-height: 14px; text-decoration: none; display: block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;border-bottom: 3px solid #FFFFFF; text-align: center;">1</a>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div style="margin-bottom: 0px; line-height: 30px; font-size: 30px;">&nbsp;</div>
                                                        </div>

                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <!--[if gte mso 11]>
                </td></tr></table>
                </center>
                <![endif]-->
            </center>
        </td>
    </tr>
    </tbody>
</table>

Din åsikt är mycket viktig för oss, vänligen betygsätt <b>kursen</b> 1-5 med stjärnor.
Tack för att du hjälper oss att bli bättre!

<b>Med vänliga hälsningar
    Körkortsjakten</b>

@endcomponent

