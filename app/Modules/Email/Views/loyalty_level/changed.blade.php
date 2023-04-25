@component('mail::message', ['email' => $email])

@if($loyaltyLevel === 'silver')
    Grattis {{$name}}!
    <br/><br/>
    Ni har nu uppnått nästa medlemsnivå, <b>SILVER</b>, stort grattis till ännu fler förmåner som medför med denna medlemsnivå.
    <br/><br/>
    Som Silver medlem kommer ni få detta utöver det ni redan har idag med Körkortsjakten:
    <br/>
    <br/><b>• Silvermedalj diplom utskickat till er med Ram, som ni kan skryta med och hänga upp på skolan.</b>
    <br/><b>• 2st biobiljetter</b>
    <br/><b>• 1% rabatt på provisionskostnaden&#42;</b>
    <br/><br/>
        Vi kommer skicka dessa till er, samt provisionsprocenten ändras automatiskt i systemet
    <br/><br/>
        Medlemsnivå förnyas och börjar om varje år 1 maj.
    <br/><br/>
        <i>&#42;Gäller alla kurser för B-körkort, ej paket och körlektioner.</i>
    <br/><br/>
        Glöm inte att uppdatera ert utbud med kurser och körlektioner, så vi kan förmedla nya elever till er, och att ni kan nå nästa medlemsnivå med ännu bättre förmåner.
    <br/><br/>
        Återigen stort grattis och vi vill passa på att tacka er för er lojalitet som kund hos oss på Körkortsjakten.
@elseif($loyaltyLevel === 'gold')
    Grattis {{$name}}!
    <br/><br/>
    Ni har nu uppnått nästa medlemsnivå, <b>GULD</b>, stort grattis till ännu fler förmåner som medför med denna medlemsnivå.
    <br/><br/>
    Som Guld medlem kommer ni få detta utöver det ni redan har idag med Körkortsjakten:
    <br/>
    <br/><b>• Guld diplom utskickat till er med Ram, som ni kan skryta med och hänga upp på skolan</b>
    <br/><b>• Presentkort Åhléns 800:-</b>
    <br/><b>• 1,5% rabatt på provisionskostnaden&#42;</b>
    <br/><br/>
        Vi kommer skicka dessa till er, samt provisionsprocenten ändras automatiskt i systemet.
    <br/><br/>
        Medlemsnivå förnyas och börjar om varje år 1 maj.
    <br/><br/>
        <i>&#42;Gäller alla kurser för B-körkort, ej paket och körlektioner.</i>
    <br/><br/>
        Glöm inte att uppdatera ert utbud med kurser och körlektioner, så vi kan förmedla nya elever till er, och att ni kan nå nästa medlemsnivå med ännu bättre förmåner.
    <br/><br/>
        Återigen stort grattis och vi vill passa på att tacka er för er lojalitet som kund hos oss på Körkortsjakten.
@elseif($loyaltyLevel === 'diamond')
    Grattis {{$name}}!
    <br/><br/>
    Ni har nu uppnått nästa medlemsnivå, <b>DIAMANT</b>, stort grattis till ännu fler förmåner som medför med denna medlemsnivå.
    <br/><br/>
    Som Diamant medlem, högsta nivån, kommer ni få detta utöver det ni redan har idag med Körkortsjakten:
    <br/>
    <br/><b>• DIAMANT diplom utskickat till er med Ram, som ni kan skryta med och hänga upp på skolan</b>
    <br/><b>• Presentkort hotell övernattning 1200:-</b>
    <br/><b>• 2% rabatt på provisionskostnaden&#42;</b>
    <br/><br/>
        Vi kommer skicka dessa till er, samt provisionsprocenten ändras automatiskt i systemet.
    <br/><br/>
        Medlemsnivå förnyas och börjar om varje år 1 maj.
    <br/><br/>
        <i>&#42;Gäller alla kurser för B-körkort, ej paket och körlektioner</i>
    <br/><br/>
        Glöm inte att uppdatera ert utbud med kurser och körlektioner, så vi kan förmedla nya elever till er.
    <br/><br/>
        Återigen stort grattis och vi vill passa på att tacka er för er lojalitet som kund hos oss på Körkortsjakten.
@endif


@endcomponent
