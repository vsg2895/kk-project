@component('mail::message', ['email' =>  $event->rating->user->email])

# Hej!

Ni har fått ett omdöme på er sida.

{{ $event->rating->title }}

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
                                                            <div style="text-align: center; margin: 0; font-size: 10px;  text-transform: uppercase; letter-spacing: .5px;">Rating:</div>
                                                            <div class="bottom-padding" style="margin-bottom: 0px; line-height: 7px; font-size: 7px;">&nbsp;</div>
                                                            <div style="width: 100%; text-align: center; float: left;">
                                                                <div class="rating" style="text-align: center; margin: 0; font-size: 50px; width: 275px; margin: 0 auto; margin-top: 10px;">

                                                                    <table style="border-collapse: collapse;border-spacing: 0;width: 275px; margin: 0 auto; font-size: 50px; direction: rtl;" dir="rtl">
                                                                        <tbody><tr>
                                                                            @for ($i = $event->rating->rating; $i > 0 ; $i--)
                                                                                <td style="padding: 0;vertical-align: top;" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                                                    <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                                                                        <span class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="1">
                                                                                            <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                                                                            <div lang="x-full-star" style="margin: 0;display: inline-block; width:0; overflow:hidden;float:left; display:none; height: 0; max-height: 0;">★</div>
                                                                                        </span>
                                                                                        <span class="star-number" target="_blank" lang="x-star-number" style="font-family: sans-serif;color: #AEAEAE; font-size: 14px; line-height: 14px; text-decoration: none; display: block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;border-bottom: 3px solid #FFFFFF; text-align: center;">{{ $i }}</span>
                                                                                    </div>
                                                                                </td>
                                                                            @endfor
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

Kommentar: {{ $event->rating->content }}

För att gå till er sida på körkortsjakten klicka här <a href="{{ route('organization::ratings.index') }}">sida</a>

@endcomponent
