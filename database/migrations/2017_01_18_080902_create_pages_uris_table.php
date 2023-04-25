<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesUrisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages_uris', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('page_id')->unsigned();
            $table->foreign('page_id')->references('id')->on('pages');
            $table->string('uri');
            $table->integer('status')->default(\Jakten\Models\PageUri::ACTIVE);
            $table->timestamps();
        });
        $this->insert();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages_uris', function (Blueprint $table) {
            $table->dropForeign('pages_uris_page_id_foreign');
        });
        Schema::dropIfExists('pages_uris');
    }

    public function insert()
    {
        $pages = [
            [
                'page' => [
                    'title' => 'Friskrivningsklausul',
                    'meta_description' => '',
                    'content' => '
<div class="container">
    <h2>Friskrivningsklausul</h2>
    <p>När det gäller de tjänster som listas hos körkortsjakten så ansvarar varje trafikskola för att tjänsterna samt marknadsföring och försäljning av deras tjänster uppfyller gällande lagar och regler. Trots att körkortsjakten.se alltid gör sitt yttersta för att informationen om tjänsterna ska vara korrekta så kan det hända att felaktigheter smyger sig in. Det är alltid den information som återfinns vid tillfället för uppdateringen av priser på körskolornas webbplatser som körkortsjakten avser i sin prisjämförelse.</p>

    <h2>Policy</h2>
    <p>Vår ambition och vision är att vara den naturliga mötesplatsen för kommande elever och trafikskolor. Vårt mål är att lista samtliga trafikskolor med priser och annan relevant information som faller inom kategorin körkort, oavsett om de sponsrar Körkortsjakten på något sätt eller inte. Därigenom försöker vi uppnå att ge våra besökare en heltäckande bild av vad trafikskolor erbjuder.</p>
    <p>Vi strävar efter att alla priser och annan relevant information ska vara korrekt. Dock kan vi inte garantera att så alltid är fallet. Har du råkat ut för något problem som informationen presenterar är vi tacksamma över om du rapporterar det till oss, så att vi kan rätta till felaktig information.</p>
</div>
                    ',
                ],
                'uri' => [
                    'uri' => '/friskrivning',
                    'status' => \Jakten\Models\PageUri::ACTIVE,
                ],
            ],
            [
                'page' => [
                    'title' => 'Användarvillkor',
                    'meta_description' => '',
                    'content' => '
<div class="container">
    <h1 class="page-title">Körkortsjaktens användarvillkor</h1>
    <h3 class="text-center">Senast ändrad 12 April 2017</h3>
    <p class="text-center">Välkommen till Körkortsjakten!</p>
    <p>Denna tjänst för bokning och betalning drivs av Körkortsjakten. Körkortsjakten är registrerat i Sverige med organisationsnummer 556569-2125 och vår registrerade adress är Repslagargatan 11, Stockholm.</p>
    <p>Följande villkor och bestämmelser reglerar användningen av tjänsten, vilken tillhandahålls av Körkortsjakten på uppdrag av de tjänsteutövare som är publicerade på Körkortsjakten och som erbjuder bokning och betalning av sina tjänster. Genom att använda Bokningstjänsten anses du ha godkänt dessa villkor och bestämmelser.</p>

    <h3>Din bokning</h3>
    <p>Vi ber dig notera att de tjänster som du bokar genom att använda dig av Bokningstjänsten tillhandahålls av tjänsteutövaren direkt och inte av Körkortsjakten. När du använder Bokningstjänsten för att boka en tjänst hos den Tjänsteutövare du har valt, ingår du och tjänsteutövaren därför ett direktavtal som Körkortsjakten inte utgör part till. Tjänsteutövaren måste följa lagen om köp på distans och det upprätthålls med ett avtal mellan Körkortsjakten och tjänsteutövaren.</p>
    <p>Vi ber dig också att notera att eventuell betalning av tjänster bokade via bokningstjänsten hanteras av Klarna på uppdrag av tjänsteutövaren.</p>
    <p>För att göra en Bokning med hjälp av Bokningstjänsten måste du ha rättskapacitet att göra detta och du måste åta dig ekonomiskt ansvar för alla transaktioner som görs i ditt namn. Du måste säkerställa att alla uppgifter som du lämnar till oss är sanna och korrekta.</p>

    <h3>Personuppgifter</h3>
    <p>För att kunna använda vår Tjänst måste du registrera vissa ("Uppgifter"), såsom ditt namn, din e-postadress och, i vissa fall, ett lösenord. Alla Uppgifter som du lämnar till oss kommer att användas av oss i enlighet med dessa Användarvillkor samt, i tillämpliga fall, i enlighet med villkoren i alla sekretesspolicys som gäller för Tjänsteutövare och/eller Bokningspartner.</p>
    <p>I enlighet med Personuppgiftslagen (1998:204) informeras du om att de uppgifter som du lämnar vid bokning av tjänst kommer att behandlas konfidentiellt för ändamålet så att Din kundrelation med Körkortsjakten och/eller den trafikskola som du bokar tid hos skall fungera på ett tillfredsställande sätt beträffande tjänsten. När Du i samband med bokning lämnar personuppgifter om dig själv, lämnar Du samtycke enligt Personuppgiftslagen (1998:204).</p>
    <p>Du kommer att få e-post och/eller textmeddelanden, inklusive förfrågningar att lämna omdömen, relaterade till fullföljandet av din bokning och din användning av Bokningstjänsten, och vi kan komma att använda dina uppgifter i det syftet.</p>

    <h3>Cookies</h3>
    <p>På samma sätt som andra kommersiella hemsidor använder vår Tjänst en standardteknik kallad &rdquo;cookies&rdquo; samt webbserverloggar för att samla in information om hur vår Tjänst används så att vi ska kunna förbättra tjänsten. Information som samlas in via cookies och webbserverloggar kan innehålla tid och datum för besöken, vilka sidor som besökts, hur lång tid som använts för användning av tjänsten samt vilka hemsidor som besökts innan och efter du använde vår Tjänst. Cookies ger oss inte möjlighet att samla in några personuppgifter om dig och vi sparar inte några personuppgifter som vi får från dig via cookies.</p>

    <h3>Frågor</h3>
    <p>Läs mer om Klarnas olika betalningsalternativ. Besök www.klarna.com/se.</p>
    <p>Vid frågor till Klarna om dina betalningar Tel 08-120 120 10</p>
    <p>Vid frågor till Körkortsjakten kontakta oss på kontakt@korkortsjakten.se</p>

    <h3>Vår adress </h3>
    <p><strong>Körkortsjakten<br>
    &#8453; SPIUT Management<br>
    Repslagargatan 11<br>
    118 46 Stockholm</strong></p>
</div>
                    ',
                ],
                'uri' => [
                    'uri' => '/villkor',
                    'status' => \Jakten\Models\PageUri::ACTIVE,
                ],
            ],
            [
                'page' => [
                    'title' => 'Avtalsvillkor mellan Trafikskolan och Körkortsjakten',
                    'meta_description' => '',
                    'content' => '
<div class="container">
    <h1 class="page-title">Avtalsvillkor mellan Trafikskolan och Körkortsjakten</h1>

    <h3>Försäljningsvillkor för kurser, produkter och tjänster på www.korkortsjakten.se</h3>
    <p>Villkoren gäller för förmedling av köp mellan korkortjakten.se och trafikskolan.</p>

    <h3>Kundkonto</h3>
    <p>För att administrera och sälja tjänster via korkortsjakten.se måste trafikskolan skapa ett konto samt godkänna köp- och försäljningsvillkor och sekretesspolicyn. Genom att skapa ett kundkonto godkänner Trafikskolan att följa lagstiftning och förbinder sig attfölja lagen om reklamationer, öppet köp och andra konsumentlagar som gäller förhandel med varor eller tjänster. Samt de <a href="/kopvillkor">köpvillkor</a> som Körkortsjakten använder.</p>
    <p>
    Vi ber dig notera att de tjänster som du bokas genom Körkortsjakten tillhandahålls av tjänsteutövaren direkt och inte av Körkortsjakten. När du använder Bokningstjänsten för att lägga upp en produkt eller kurs ingår du och konsumenten i ett direktavtal som Körkortsjakten inte utgör part till.

    Vi ber dig också att notera att eventuell betalning av tjänster bokade via bokningstjänsten hanteras av Klarna.
    </p>

    <h3>Uppsägning</h3>
    <p>Tjänsten kan sägas upp med en månads uppsägningstid, när endera parten så önskar. Uppsägning sker skriftligen via mail till kontakt@korkortsjakten.se I samband med uppsägning upphör alla tilläggstjänster att gälla. Detta gäller dock inte prisjämförelse och ranking.</p>

    <h3>Pris och varubeskrivning</h3>
    <p>Vi accepterar endast varor och tjänster som beskrivits och prissatt av trafikskolan själv. Det priset trafikskolan har sålt en kurs eller vara för måste stämma överens med vad slutkunden betalar.</p>

    <h3>Provision</h3>
    <p>Körkortsjaktens prisjämförelse inkl. ranking är gratis och ingår som standard för samtliga trafikskolor. Då en trafikskola är registrerad som medlem ingå bl.a. förmedling av bokningar och lektioner eller andra tilläggstjänster. När en tjänst eller vara säljs via Körkortsjakten.se, utgår en provision om 12% av priset för den tjänst eller vara som förmedlats via Körkortsjakten.se. Provision faktureras månatligen i efterskott. Samtliga priser exklusive moms.</p>

    <h3>Utbetalning till trafikskolan</h3>
    <p>Utbetalning sker senast i samband med utförd kurs eller levererad vara och utbetalning sker automatiskt från Körkortsjaktens partner Klarna.</p>

    <h3>Bokning av kurs</h3>
    <p>Vid bokning eller köp av kurs eller försäljning av vara, bekräftar Körkortsjakten slutkundens bokning till både slutkund och trafikskola. Kurser skall innehålla bokningsinformation såsom tid och plats för utbildningen. I bokningsbekräftelsen till trafikskola finns slutkundens kontaktuppgifter. Vid en bokning ingås ett avtal mellan slutkund och trafikskolan. Det är trafikskolan som meddelar eleven vid eventuellt förhinder.</p>

    <h3>Ångerrätt och avbokning</h3>
    <p>Trafikskolan använder sig av Körkortsjaktens avbokningsregler vilket framgår av bekräftelse till slutkund.</p>

    <h3>Trafikskolans kunduppgifter</h3>
    <p>Som medlem på Körkortsjakten är det trafikskolans ansvar att alla uppgifter
    publicerade på Trafikskolans sida på Körkortsjakten.se är korrekta och
    fullständiga. Trafikskolan kan alltid ändra och korrigera uppgifterna under Min
    dashboard. Om trafikskolan uppger ogiltig eller vilseledande information kan
    Körkortsjakten utan föregående förvarning stänga av Trafikskolans konto. Läs mer
    om hur vi behandlar kontouppgifter, i vår sekretesspolicy.</p>

    <h3>Ändring av villkor</h3>
    <p>Körkortsjakten kan när som helst komma att ändra dessa villkor. Trafikskolan
    meddelas detta vid nästa inloggning, och ombedjes då godkänna ändringarna.</p>

    <h3>Kundservice</h3>
    <p>Vid eventuella frågor ska trafikskolan kontakta korkortsjakten.se Customer Support
    via e-post till kontakt@korkortsjkaten.se. Märk e-post meddelandet med Customer
    Support.</p>


    <h2>Sekretesspolicy</h2>

    <h3>Vilka uppgifter behandlar vi och varför?</h3>
    <p>När blir medlem hos korkortsjakten.se ber vi dig att lämna vissa personuppgifter, t.ex.
    namn, adress, e-postadress och kortuppgifter.</p>
    <p>Vi sparar och använder dessa uppgifter för att göra det möjligt för dig att köpa och
    sälja varor, för att kommunicera med dig och för att skicka vårt nyhetsbrev om du
    lämnat ditt samtycke till detta. Vi registrerar vilka köp och försäljningar du genomför
    så att du kan få en överblick över tidigare köp och försäljningar när du loggar in på
    ditt konto. Dessutom använder vi uppgifterna till statistik och för att förbättra
    användarupplevelsen på vår webbplats.</p>

    <h3>Spridning av personuppgifter</h3>
    <p>Vi lämnar bara ut Trafikskolans uppgifter till företag som t.ex. samarbetspartners,
    transportföretag, och vår betalningspartner KLARNA, och endast i sådan
    utsträckning som det är nödvändigt för att behandla köp och försäljningar eller
    säkerställa en optimal drift.</p>

    <h3>Cookies</h3>
    <p>Vi använder cookies för att ge användarna en så bra upplevelse som möjligt när de
    besöker vår hemsida. En cookie är en liten textfil som sparas på din dators hårddisk,
    på din smartphone eller annan IT-utrustning. Den gör det möjligt att känna
    besökarens dator/IP-adress och samla information om vilka sidor de besöker och
    vilka funktioner de använder. Om du inte vill acceptera cookies kan man stänga av
    cookies i din webbläsare. Observera att du i så fall inte kan använda vissa tjänster
    och funktioner, eftersom de använder cookies för att komma ihåg dina val.</p>


    <h2>Kontakt</h2>
    <p>Om du vill ha mer information eller har några andra frågor är du mycket välkommen
    att kontakta oss på kontakt@korkortsjakten.se</p>
    <p>Frida Andersson<br>
    Körkortsjakten<br>
    <a href="tel: 0046763463391">+46 (0)76 346 33 91</a></p>
</div>
                    ',
                ],
                'uri' => [
                    'uri' => '/villkor-trafikskola',
                    'status' => \Jakten\Models\PageUri::ACTIVE,
                ],
            ],
            [
                'page' => [
                    'title' => 'Köpvillkor',
                    'meta_description' => '',
                    'content' => '
<div class="container">
    <h1 class="page-title">Köpvillkor</h1>
    <h3>Din bokning</h3>
    <p>Vi ber dig notera att de tjänster som du bokar genom att använda dig av Bokningstjänsten tillhandahålls av tjänsteutövaren direkt och inte av Körkortsjakten. När du använder Bokningstjänsten för att boka en tjänst hos den Tjänsteutövare du har valt, ingår du och tjänsteutövaren därför ett direktavtal som Körkortsjakten inte utgör part till. Vi ber dig också att notera att eventuell betalning av tjänster bokade via bokningstjänsten hanteras av Klarna på uppdrag av tjänsteutövaren.
    För att göra en Bokning med hjälp av Bokningstjänsten måste du ha rättskapacitet att göra detta och du måste åta dig ekonomiskt ansvar för alla transaktioner som görs i ditt namn. Du måste säkerställa att alla uppgifter som du lämnar till oss är sanna och korrekta. Nedan följer Trafikskolans köpvillkor för de tjänster eller produkter du köper på www.korkortsjakten.se</p>

    <h3>Priser och betalning</h3>
    <p>Varje vara anges med pris inklusive moms. I kundvagnen kan man se det totala priset inklusive alla avgifter, moms, frakt och betalning. Betalningsvillkor finns angiven i kundvagnen beroende av valt betalningssätt.</p>

    <h3>Ångerrätt</h3>
    <p>Din ångerrätt (ångerfristen) gäller under 14 dagar. I ditt meddelande till oss måste det klart framgå att du ångrar dig. Ångerfristen börjar löpa den dag du tog emot varan eller en väsentlig del av den. Om avtalet gäller en specialtillverkad vara – eller en vara som fått en tydlig personlig prägel, börjar ångerfristen löpa den dag vi lämnat information.</p>
    <p>
        Du har inte ångerrätt om:
        Tjänsten påbörjats under ångerfristen med ditt samtycke, priset är beroende av finansmarknadens svängningar, till exempel en aktie, varan på grund av sin beskaffenhet inte kan återlämnas, förseglingen brutits på ljud- eller bildupptagningar eller datorprogram. Med försegling avses även teknisk plombering.
    </p>
    <p>
        När du utnyttjat din ångerrätt:
        Du är skyldig att hålla varan i lika gott skick som när du fick den. Du får inte använda den, men naturligtvis försiktigt undersöka den. Om varan skadas eller kommer bort på grund av att du är vårdslös förlorar du ångerrätten.
    </p>

    <h3>Att tänka på</h3>
    <p>Vi ber dig tänka på att komma i god tid till din kurs och ta med dig en kopia av e-postbekräftelsen och ta alltid med legitimation.
    Eftersom ditt avtal är tecknat direkt med Tjänsteutövaren ska alla frågor som du kan tänkas ha i förbindelse med din bokning ställas direkt till Tjänsteutövaren med användande av de kontaktuppgifter som anges i e-postbekräftelsen.</p>

    <h3>Privat Policy</h3>
    <p>När du lägger din beställning hos oss uppger du dina personuppgifter. I samband med din registrering och beställning godkänner du att vi lagrar och använder dina uppgifter i vår verksamhet för att fullfölja avtalet gentemot dig. Du har enligt Personuppgiftslagen rätt att få den information som vi har registrerat om dig. Om den är felaktig, ofullständig eller irrelevant kan du begära att informationen ska rättas eller tas bort. Kontakta oss i så fall via e-post.</p>

    <h3>Avbokningspolicy</h3>
    <p>Om du önskar avboka en bokad kurs ber vi dig att göra detta i god tid.</p>
    <p>Kursavgifter är återbetalningsbara under vissa omständigheter enligt nedan.</p>
    <ul>
        <li>En bokning är officiellt avbokad när du klickar på avbokningsknappen på din dashboard och avbokningen har bekräftas till dig via mail eller sms.</li>
        <li>För en 100% återbetalning av avgifter för en kurs eller lektion måste avbokning göras senast 24 timmar före kursens starttid. Om kursen t.ex. är på fredag kl 18:00 måste du avboka senast på onsdagen innan kl 18:00.</li>
        <li>Om deltagaren påbörjar men väljer att avbryta en kurs tidigare än planerat sker ingen återbetalning.</li>
    </ul>

    <h3>Leveranser</h3>
    <p>Leveranstiden anges i kundvagnen under respektive fraktsätt. Om en vara har avvikande leveranstid står det angivet vid respektive vara.</p>
    <p>Ej uthämtade paket
    Har du valt postförskott som fraktsätt finns dina varor kvar på posten under fyra veckor. Ej hämtade varor returneras till oss. För alla paket som inte löses ut förbehåller vi oss rätten att debitera dig kostnader för returfrakt, expeditionsavgift och hanteringsavgift, för närvarande 150:-</p>

    <h3>Returer</h3>
    <p>Returer sker på din egen bekostnad utom om varan är defekt eller om vi har packat fel. Returer ska skickas som brev eller paket, inte mot postförskott. Vid byten betalar vi den nya frakten från oss till dig.</p>

    <h3>Cookies</h3>
    <p>På samma sätt som andra kommersiella hemsidor använder vår Tjänst en standardteknik kallad ”cookies” samt webbserverloggar för att samla in information om hur vår Tjänst används så att vi ska kunna förbättra tjänsten. Information som samlas in via cookies och webbserverloggar kan innehålla tid och datum för besöken, vilka sidor som besökts, hur lång tid som använts för användning av tjänsten samt vilka hemsidor som besökts innan och efter du använde vår Tjänst. Cookies ger oss inte möjlighet att samla in några personuppgifter om dig och vi sparar inte några personuppgifter som vi får från dig via cookies.</p>

    <h3>Betalningsätt</h3>
    <p>Läs mer om Klarnas olika betalningsalternativ. Besök <a target="_blank"  href="https://www.klarna.com/se">Klarna</a></p>

</div>
                    '
                ],
                'uri' => [
                    'uri' => '/kopvillkor',
                    'status' => \Jakten\Models\PageUri::ACTIVE,
                ],
            ],
            [
                'page' => [
                    'title' => 'Klarna',
                    'meta_description' => '',
                    'content' => '
<div class="container">
    <div class="text-xs-center">
        <h1 class="page-title text-primary">Nu kan du äntligen ta betalt direkt via Klarna.</h1>
        <p class="lead">Betalning via faktura, kort samt internetbank</p>
        <p>Klarna är ledande i Europa på fakturabaserade betallösningar för e-handel. Med deras säkra och enkla köpprocess kan du väsentligt öka din försäljning. I kassan behöver dina kunder bara uppge sådan information de kan utantill, som personuppgifter och e-postadress och de behöver inte betala förrän de mottagit och godkänt varan. Klarna tar all kredit- och bedrägeririsk och garanterar att du alltid får betalt. Idag har fler än 7,5 miljoner konsumenter handlat med Klarna i över 15.000 e-butiker i 7 olika länder.</p>
    </div>
</div>

<div class="section-gray">
    <div class="container">
        <img src="/build/img/klarna-logo-tagline.svg" width="320" class="float-xs-right mb-1"/>
        <h2>Varför välja Klarna?</h2>
        <h3 class="mb-0">Ökar din försäljning</h3>
        <p>– Ökar din konvertering genom att göra det säkrare och enklare för dina kunder att handla online.</p>
        <h3 class="mb-0">Ingen risk</h3>
        <p>– Klarna tar all risk och garanterar att du alltid får betalt oavsett om slutkunden betalar eller inte.</p>
        <h3 class="mb-0">Enkelt att komma igång och använda</h3>
        <p>– Då Körkortsjakten redan har Klarna förinstallerad är det mycket smidigt att integrera. Helt automatiserade fakturor kommer även att minska din dagliga administration.</p>

        <hr class="section-divider">

        <h2>Vilka tjänster erbjuder Klarna?</h2>

        <div class="clearfix mb-2">
            <svg class="float-xs-left mr-2" xmlns="http://www.w3.org/2000/svg" width="58" height="60" viewBox="0 0 29 30">
              <g>
                <path d="M20.5,3.5l2-2a2.55,2.55,0,0,1,4,0C27.56,2.84,27,4,26,5L24.5,6.5" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M.5,2.5h4L7,18.5a4.1,4.1,0,0,0,4,3H25.5" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M5.5,6.5h23L27,14.56A2.34,2.34,0,0,1,24.5,16.5H7" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M13.5,26.5a3,3,0,1,1-3-3,3,3,0,0,1,3,3Z" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M25.5,26.5a3,3,0,1,1-3-3,3,3,0,0,1,3,3Z" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <polyline points="16.5 6.5 10.5 0.5 6.5 4.5 8.5 6.5" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <polyline points="15.5 5.5 15.5 0.5 20.5 0.5 20.5 6.5" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
              </g>
            </svg>
            <div class="section-text">
                <h3>Klarna checkout</h3>
                <p>Klarna Checkout är en ny revolutionerande betallösning som förändrar hur vi handlar på nätet. Med hjälp av s k ”Intelligent Identification” kan man med hjälp av endast ett fåtal frågor identifiera kunden, bekräfta köpet till din butik och därefter fullföljs betalningen. Genom att separera själva köpet från betalningen ökar din konvertering väsentligt och risken för avhopp i kassan minskar. Med Klarna Checkout erbjuder du alla populära betalsätt via endast en leverantör, något som ger en minskad administration och mer tid över att fokusera på din webbutik. Körkortsjakten är sk Klarna Native Partner och kan därför erbjuda Klarna Checkout utan start eller månadsavgift från Klarna.</p>
            </div>
        </div>

        <div class="clearfix mb-2">
            <svg class="float-xs-left mr-2" xmlns="http://www.w3.org/2000/svg" width="48" height="60" viewBox="0 0 24 30">
              <g>
                <path d="M23.5,20A2.5,2.5,0,1,1,21,17.5,2.5,2.5,0,0,1,23.5,20Z" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <polyline points="20 22.5 18.5 29.5 21 28.5 23.5 29.5 22 22.5" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <polyline points="16.5 26.5 0.5 26.5 0.5 7.5 7.5 0.5 21.5 0.5 21.5 15.5" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <polyline points="0.5 7.5 7.5 7.5 7.5 0.5" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2.5,13.5h6" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2.5,18.5h14" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2.5,23.5h5" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M13.5,9.5c.06.65.93,1,2,1s2-.56,2-1.25c0-1-1.65-1.19-2-1.25s-2-.23-2-1.25c0-.69.9-1.25,2-1.25s1.93.35,2,1" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M15.5,4.5v7" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
              </g>
            </svg>
            <div class="section-text">
                <h3>Klarna faktura</h3>
                <p>Med faktura betalar kunden 14 dagar efter köpet. Faktura är det mest efterfrågade betalsättet online. Som kund behöver man endast uppge sådan information man kan utantill, såsom namn, adress och födelsedatum. Efter en sekundsnabb intern prövning godkänns köpet och du kan skicka varorna som kunden nu har 14 dagar på sig att betala. Förutom detta tar Klarna hela kredit- och bedrägeririsken. Butiken slipper all administration och risk i samband med faktureringen till ett vettigt pris. Enklare kan det inte bli! </p>
            </div>
        </div>

        <div class="clearfix mb-2">
            <svg class="float-xs-left mr-2" xmlns="http://www.w3.org/2000/svg" width="58" height="58" viewBox="0 0 29 29">
              <g>
                <path d="M6.5,11.5V4.17A3.84,3.84,0,0,1,10.5.5h8a3.84,3.84,0,0,1,4,3.67V11.5" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8.5,8.5h12" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <polyline points="24.5 8.5 28.5 8.5 28.5 28.5 0.5 28.5 0.5 8.5 4.5 8.5" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12.5,19.33c.07.65.93,1.17,2,1.17s2-.56,2-1.25c0-1-1.65-1.19-2-1.25s-2-.23-2-1.25c0-.69.9-1.25,2-1.25s1.93.52,2,1.17" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14.5,14.5v7" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M20.5,18.5a6,6,0,1,1-6-6,6,6,0,0,1,6,6Z" fill="none" stroke="#4F7FC1" stroke-linecap="round" stroke-linejoin="round"/>
              </g>
            </svg>
            <div class="section-text">
                <h3>Klarna konto</h3>
                <p>Gör dina kunder köpstarka med delbetalning!<br/>
                När man handlar med Klarna konto får man en faktura med månadens samlade köp. Denna kan betalas direkt eller delas upp i mindre delbetalningar. Med konto ger du din kund möjlighet till fler köp med högre snittordervärde. Klarna tar kreditrisken, det innebär att du alltid får betalt oavsett om inte kunden betalar. Körkortsjakten installerar Konto funktionen kostnadsfritt i samband med installation av faktura tjänsten.</p>
                <p>Öka din konvertering genom att göra det säkrare och enklare för dina kunder att handla på Internet samt se till att erbjuda alla betalsätt som kunderna önskar betala med. Det kan se lite olika ut beroende på marknader och länder men i Sverige är det vanligaste betalsätten faktura, kortbetalning samt direktbetalning via bank. Även delbetalning är populärt, framförallt vid högre snittordrar.</p>
            </div>
        </div>

    </div>
</div>

<div class="text-xs-center"><a class="btn btn-lg btn-primary" href="/registrera/organisation">Registrera din trafikskola nu &rarr;</a></div>
            ',
        ],
        'uri' => [
            'uri' => '/klarna',
            'status' => \Jakten\Models\PageUri::ACTIVE,
        ],
    ],
    [
        'page' => [
            'title' => 'FAQ',
            'meta_description' => '',
            'content' => '
<div class="container">
    <h1 class="page-title">FAQ</h1>

    <h3>1. Hur gör jag för att lägga upp min skola på Körkortsjakten?</h3>
    <p>Att få en profil med sina prisuppgifter och kontaktuppgifter på Körkortsjakten är kostnadsfritt och enkelt. Vår ambition och vision är att vara den naturliga mötesplatsen för kommande elever och trafikskolor. Vårt mål är att lista samtliga trafikskolor med priser och annan relevant information som faller inom kategorin körkort, oavsett om de sponsrar Körkortsjakten på något sätt eller inte. Därigenom kan vi garantera att våra besökare får en heltäckande bild av vad trafikskolor erbjuder och en ge en oberoende prisjämförelse.</p>


    <h3>2. Hur gör jag för att rapportera felaktiga priser på Körkortsjakten?</h3>
    <p>Körkortsjakten gör alltid gör sitt yttersta för att informationen ska vara korrekt men det kan hända att felaktigheter uppstår. Trafikskolorna hålls inte ansvariga för felaktig information hos Körkortsjakten utan det är alltid den information som återfinns på trafikskolornas webbplatser eller vid direkt kontakt som gäller vid publicering på Körkortsjakten. För att underlätta uppdateringar för trafikskolorna kan de själva gå in och redigera sin profil gratis genom att maila till kontakt@korkortsjakten.se Dessa uppgifter undersöks av Körkortsjakten och publiceras förutsatt att de är korrekta.</p>


    <h3>3. Hur gör jag för att lägga upp mina kurstillfällen på Körkortsjakten?</h3>
    <p>Att lägga upp tider är kostnadsfritt för trafikskolorna och det finns ingen bindningstid. Ni kan när som helst lägga upp tider hos oss och när som helst ta bort dem. Det ända du behöver göra är att maila in dina tider, pris för kursen, och hur eleven ska betala. De flesta väljer att ta betalt med bankgiro i förväg och då behöver vi även bankgiro nummer. Skicka in dina uppgifter till kontakt@korkortsjakten.se</p>


    <h3>4. Vad behöver ni ha för uppgifter för att jag ska kunna lägga upp mina tider för handledarkurs/introduktionskurs?</h3>
    <p>Pris, datum, tid, längd på kursen, betalningssätt för eleven. Skicka in till kontakt@kortkortsjakten.se</p>


    <h3>5. Hur betalar eleven kursen till mig?</h3>
    <p>Det finns två sätt för trafikskolor som är anslutna till samarbete med Körkortsjakten. Alternativ1 är ni önska att allt ska fungera precis som vanligt och då kommer trafikskolan själva ha hand om betalningen, Körkortsjakten har inte med betalningen att göra utan endast bokningen. Alternativ2 är att trafikskolan önskar att Körkortsjakten tar hand om hela betalningen och slipper på så sätt all administration kring fakturering, påminnelser m.m.</p>


    <h3>6. Hur kan det vara gratis att lägga upp sina kurser på Körkortsjakten?</h3>
    <p>Just nu så är det gratis för alla trafikskolor att vara med och erbjuda sina tider på Körkortsjakten I dagsläget så fokuserar Körkortsjakten på att visa trafikskolorna hur många bokningar de faktiskt får via oss. Vi vill visa hur viktig Körkortsjakten har blivit för branschen. När en besökare bokar en kurs på Körkortsjakten tas en reservationsavgift ut av den som bokar på 40kr. Den avgift är till för att täcka underhållet på kalendersystemet, kunde erbjuda er trafikskolor att enkelt kunna lägga upp de tider ni önskar samt att den stor del går till marknadsföring till sidan och marknadsföring till de skolor som är anslutna till oss. Avgiften är en reservationsavgift/bokningsavgift som fungerar precis som när man exempelvis reserverar biobiljetter på SF för en filmupplevelse.</p>


    <h3>7. Vad händer om en kurs blir full?</h3>
    <p>Du måste själv ansvara för att kontakta Körkortsjakten om en kurs blir full. Du kan kontakta oss via mail dygnet runt och vi svarar så snabbt vi kan. Inom kort kommer trafikskolor själv kunna gå in och redigera sina tider.</p>


    <h3>8. Hur mycket satsar Körkortsjakten på Marknadsföring?</h3>
    <p>Marknadsföringen på internet har blivit en av de absolut viktigaste marknadsföringskanalerna. Körkortsjakten har stor erfarenhet av onlinemarknadsföring och om vad som fungerar i olika typer av digitala medier. Vi vet vad som gör en hemsida framgångsrik och användbar. Vi arbetar dagligen med SEO och googleoptimering vilket ger anslutna Trafikskolor en framskjuten position på nätet. Körkortsjakten finns alltid i samma rum där våra besökare finns. Vi arbetar löpande med sociala medier, bloggar, bannerannonsering och sökordsoptimering. Vi annonserade i senast på ungdomar.se som är Sveriges största ungdomssida. Genom att trafikskolan syns på Körkortsjakten kommer den automatiskt alltid att synas i våra sammanhang för kommande elever.</p>


    <h3>9. Hur gör jag om jag vill annonsera på Körkortsjakten?</h3>
    <p>Om du är intresserad av att annonsera på Körkortsjakten hör av dig till oss på kontakt@korkortsjakten.se</p>


    <h3>10. Hur kan min trafikskola hamna högre upp på Körkortsjakten?</h3>
    <p>Körkortsjakten är en oberoende prisjämförelse. Din trafikskola kan inte hamna högre upp om trafikskolan inte sänker priserna. En trafikskola kan dock förbättra sina chanser att synas mer på Körkortsjakten. Är du intresserad på att veta hur? Maila kontakt@korkortsjakten.se</p>
    
    <hr class="section-divider">
    
    <p>Har du frågor kring utbetalningar, orderhanteringar eller faktura Klarna? Här får du svar på de vanligaste frågorna.</p>
    <a class="btn btn-primary" target="_blank"  href="https://help.klarna.com/sv/k%C3%B6rkortsjakten">Klarna FAQ →</a>
</div>
                    ',
                ],
                'uri' => [
                    'uri' => '/faq',
                    'status' => \Jakten\Models\PageUri::ACTIVE,
                ],
            ],
            [
                'page' => [
                    'title' => 'Om oss',
                    'meta_description' => '',
                    'content' => '
<div class="container">
    <h1 class="page-title">Om Körkortsjakten</h1>
    <p class="lead">Hitta, jämför och boka på ett och samma ställe</p>

    <p>Körkortsjakten erbjuder en mötesplats för trafikskolor och elever. Hos Körkortsjakten kan trafikskolor erbjuda sina produkter och tjänster. Plattformen erbjuder oberoende jämförelse av trafikskolor där det går att jämföra pris, omdöme, kurser eller geografisk närhet. Besökare på plattformen kan när man funnit en trafikskola man gillar, boka kurser och ta del av erbjudande hos den trafikskolan som passar bäst.</p>

    <div class="text-sm-center">
        <p><strong>Körkortsjakten AB<br>
        &#8453; SPIUT<br>
        Repslagargatan 11<br>
        118 46 Stockholm<br>
        Org.nr. 556569-2125</strong></p>

        <p><strong>Bankgiro nr 423-7037<br>
        Godkänd för F-skatt</strong></p>
    </div>
</div>
                    ',
                ],
                'uri' => [
                    'uri' => '/om-oss',
                    'status' => \Jakten\Models\PageUri::ACTIVE,
                ],
            ],
            [
                'page' => [
                    'title' => 'Medlemskap',
                    'meta_description' => '',
                    'content' => '
<div class="container">
    <h1 class="page-title">Medlemskap</h1>

    <div class="mb-1 text-sm-center">
        <h1 class="page-title">Kompletta lösningar för framgångsrika trafikskolor.</h1>
        <p class="lead">Körkortsjakten hjälper dig att nå er målgrupp och förmedla era tjänster.</p>

        <div><img src="/build/img/offer.png" width="495" height="426"/></div>
        <a class="btn btn-primary mt-1" href="/registrera/organisation">Bli medlem nu &rarr;</a>
    </div>
</div>

<div class="section-gray">
    <div class="container">

        <h2>Nå er målgrupp</h2>
        <p>Körkortsjakten är en mötesplats för elever och trafikskolor.  Hos oss kan eleven jämföra, hitta och boka kurser och körlektioner på ett och samma ställe. Alla trafikskolor som är medlemmar på Körkortsjakten får tillgång till följande:</p>
        <ul class="lead">
            <li>Beskrivning om er trafikskola med erbjudande och kontaktuppgifter</li>
            <li>Erbjuda kurser och tjänster</li>
            <li>Administrera egna kurser och bokningar</li>
            <li>Publicera era priser och paketerbjudande</li>
            <li>Klarnas olika betalningsalternativ</li>
            <li>Marknadsföring på Sveriges största mötesplats för trafikskolor och elever</li>
        </ul>

        <hr class="section-divider">

        <h3>Utveckla din befintliga affär på Körkortsjakten</h3>
        <p>Körkortsjakten är Sveriges största mötesplats för elever och trafikskolor.</p>
        <p>Oavsett om du är en stor eller liten trafikskola så kan Körkortsjakten hjälpa dig att utveckla din affär e-handel.</p>
        <p>När du ansluter din trafikskola till Körkortsjakten, ingår allt du behöver för att kunna sälja dina olika tjänster och produkter. Inte minst får du tillgång till marknadens smartaste kassalösning med alla populära betalsätt, helt integrerade och redo att användas.</p>

        <hr class="section-divider">

        <h3>Klarna Checkout ingår alltid helt utan startavgift eller månadsavgift</h3>
        <p>Du behöver inte koppla på någon kassalösning med betalningsfunktion. Den är redan på plats och klar att användas. Med Klarnas säkra och enkla köpprocess kan du öka din försäljning rejält. I kassan behöver dina kunder bara uppge information som de kan utantill och de behöver inte betala innan de har fått och godkänt varan. Enkelheten i detta leder till att konverteringen blir hög, dvs att en stor del av de som kommer till kassan också genomför sitt köp. Klarna tar all kredit- och bedrägeririsk och garanterar att du alltid får betalt.</p>
        <p>Genom ett unikt samarbete med Klarna där Körkortsjakten är så kallad Klarna Native Partner kan Körkortsjakten erbjuda Klarna Checkout, marknadens smidigaste kassalösning, utan start eller månadsavgift från Klarna! Du betalar därmed bara en transaktionsavgift på 13.8% per köp till Körkortsjakten, inkl Klarna.</p>
        <a class="btn btn-primary" href="/klarna">Läs mer om Klarna →</a>
        
        <hr class="section-divider">
        
        <p>Har du frågor kring utbetalningar, orderhanteringar eller faktura Klarna? Här får du svar på de vanligaste frågorna.</p>
        <a class="btn btn-primary" target="_blank"  href="https://help.klarna.com/sv/k%C3%B6rkortsjakten">Klarna FAQ →</a>
    </div>
</div>
                    ',
                ],
                'uri' => [
                    'uri' => '/medlemskap',
                    'status' => \Jakten\Models\PageUri::ACTIVE,
                ],
            ],
            [
                'page' => [
                    'title' => 'Så räknas jämförpriset',
                    'meta_description' => '',
                    'content' => '
<div class="container">
    <h1 class="page-title">Prisberäkningen</h1>
    <p>Körkortsjaktens <em>jämförpris</em> ger en ungefärlig prisbild vad ett körkort kostar hos respektive körskola (med 600 minuter körlektion). Det är dock viktigt att påpeka att den största faktorn i slutpriset inte är trafikskolans pris utan andra faktorer, så som övningkörning, trafikskolanspedagogik, förkunskaper etc.</p>
    <p>Nedan kan du läsa <em>hur</em> priset räknas ut.</p>

    <div role="tabpanel">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs mb-1" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" aria-controls="b" aria-expanded="true" data-toggle="tab" href="#tab-b" role="tab">Bil (B)</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" aria-controls="a" aria-expanded="false" data-toggle="tab" href="#tab-a" role="tab">MC (A)</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" aria-controls="am" aria-expanded="false" data-toggle="tab" href="#tab-am" role="tab">Moped (AM)</a>
            </li>
        </ul><!-- Tab panes -->
        <div class="tab-content">
            <!-- START TAB: b -->
            <div class="tab-pane active" id="tab-b" role="tabpanel">
                <table class="table table-bordered margin_top_10">
                    <tbody>
                        <tr>
                            <td class="head_row" colspan="2" style="background-color:#e1e1e1;">
                                <h2>Kostnader för tillstånd och test</h2>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="tillstand" name="tillstand"></a>Körkortstillstånd</h3>
                                <p>Betalas till Transportstyrelsen. Körkortstillstånd efter återkallelse kostar 1670 kr.</p>
                            </td>
                            <td align="right" width="100">150 kr</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="uppskrivning" name="uppskrivning"></a>Uppskrivning (kunskapsprov)</h3>
                                <p>Betalas till Trafikverket. Vardagar kostar provet 325 kr. Helgdag och vardagar efter kl. 18 kostar det 400 kr.</p>
                            </td>
                            <td align="right">325 kr</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="uppkorning" name="uppkorning"></a>Uppkörning (körprov)</h3>
                                <p>Betalas till Trafikverket. Vardagar kostar provet 800 kr. Helgdag och efter kl. 18 kostar det 1 040 kr.</p>
                                <p>Läs mer om <a target="_blank"  href="https://fp.trafikverket.se/Boka/#/licence">Boka uppkörningen</a></p>
                            </td>
                            <td align="right">800 kr</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="foto" name="foto"></a>Foto till körkortet</h3>
                                <p>Betalas till Trafikverket. Kortet tas på Förarprovskontoret innan uppskrivningen. Faktura kommer i efterhand.</p>
                            </td>
                            <td align="right">80 kr</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="tillverkning" name="tillverkning"></a>Tillverkning av körkortet</h3>
                                <p>Betalas till Transportstyrelsen.</p>
                            </td>
                            <td align="right">150 kr</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="syntest" name="syntest"></a>Syntest</h3>
                                <p>Görs i regel via en trafikskola, optiker eller vårdcentral. Priset varierar.</p>
                            </td>
                            <td align="right">100 kr</td>
                        </tr>
                        <tr>
                            <td class="head_row" colspan="2" style="background-color:#e1e1e1;">
                                <h2><a id="kostnader_trafikskolor" name="kostnader_trafikskolor"></a>Kostnader till trafikskolor</h2>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="inskrivning" name="inskrivning"></a>Inskrivningsavgift samt material för teorin</h3>
                                <p>- I priset ska tillräckligt mycket litteratur (eller andra medel) ges för att klara uppskrivningen. Körkortsjakten anger priset på Körkortsboken för 350 kr. Körkortsboken är det minsta du behöver införskaffa dig för att klara av teoriprovet.</p>
                                <p>- Det finns inga krav på lärarledda teorilektioner, datorer etc.</p>
                            </td>
                            <td align="right">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="korlektioner" name="korlektioner"></a>Körlektioner</h3>
                                <p>Övningskörning med lärare på trafikskola. I jämförpriset anges 600 minuter som standard i beräkningen. 600 minuter motsvarar 15 körlektioner á 40 minuter vilket är snittet.</p>
                                <p><strong>Kriterierna för att en <em>körlektion</em> ska få vara en del i beräkningen</strong><br>
                                - Körlektionen ska vara 40-80 minuter.<br>
                                - Lektionen ska inte behövas betalas tidigare än samma dag.<br>
                                - Det ska inte vara en del av ett paket där man på förhand måste bestämma antalet lektioner man vill ta.<br>
                                - Priset inkluderar inte extra avgifter för nattkörning.</p>
                            </td>
                            <td align="right">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="riskettan" name="riskettan"></a>Riskutbildning del 1 (Riskettan)</h3>
                                <p>Obligatorisk kurs som oftast genomförs av en trafikskola.</p>
                                <a href="/s/kurser/riskettan">Boka riskettan</a>
                            </td>
                            <td align="right">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="halkan" name="halkan"></a>Riskutbildning del 2 (Halkan)</h3>
                                <p>Obligatorisk kurs som oftast genomförs genom en trafikskola.</p>
                            </td>
                            <td align="right">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="korskolan_pris_uppkorning" name="korskolan_pris_uppkorning"></a>Körskolans pris för uppkörningen</h3>
                                <p>I många fall tar körskolan en avgift för att låna bil till uppkörning. I priset ingår ofta att man värmer upp lite innan - en kortare körlektion. Denna uppvärmning räknas in som körlektion i jämförpriset. Om körskolan inte har en bil att låna ut finns det att <a href="http://www.korkortsportalen.se/kontakt/forarenhetens-kontor/Vagverksbil/" target="_blank" >hyra på Trafikverket</a> för 800 kronor.</p>
                            </td>
                            <td align="right">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="head_row" colspan="2" style="background-color:#e1e1e1;">
                                <h2><a id="privat_korning" name="privat_korning"></a>Kostnader som <em>inte</em> räknas in i jämförpriset</h2>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h3>Privat körning</h3>
                                <p>Givetvis är det inte gratis att köra runt med privat bil: Introduktionskurs (ca 450 kr / handledare), godkännande av handledare (65 kr / handledare) samt slitage och bensin (ca 30 kr / mil) är lika så kostnader.</p>
                            </td>
                            <td align="right">&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </div><!--  END TAB: b -->
            <!-- START TAB: am -->
            <div class="tab-pane" id="tab-am" role="tabpanel">
                <p>Körkortsbehörigheten AM ger rätt att köra moped klass 1 (EU moped) som är konstruerade för en hastighet på 45 km/h</p>
                <table class="table table-bordered margin_top_10" width="100%">
                    <tbody>
                        <tr>
                            <td class="head_row" colspan="2" style="background-color:#e1e1e1;">
                                <h2>Kostnader för tillstånd och test</h2>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="tillstand" name="tillstand"></a>Körkortstillstånd</h3>
                                <p>Betalas till Transportstyrelsen. Körkortstillstånd efter återkallelse kostar 1670 kr.</p>
                                <p><a href="/korkort/#korkortstillstand">Ansök om körkortstillstånd</a></p>
                            </td>
                            <td align="right" width="100">150 kr</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="uppskrivning" name="uppskrivning"></a>Uppskrivning (kunskapsprov)</h3>
                                <p>Betalas till Trafikverket. Vardagar kostar provet 325 kr. Helgdag och vardagar efter kl. 18 kostar det 400 kr.</p>
                            </td>
                            <td align="right">325 kr</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="foto" name="foto"></a>Foto till körkortet</h3>
                                <p>Betalas till Trafikverket. Kortet tas på Förarprovskontoret innan uppskrivningen. Faktura kommer i efterhand.</p>
                            </td>
                            <td align="right">80 kr</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="tillverkning" name="tillverkning"></a>Tillverkning av körkortet</h3>
                                <p>Betalas till Transportstyrelsen.</p>
                            </td>
                            <td align="right">150 kr</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="syntest" name="syntest"></a>Syntest</h3>
                                <p>Görs i regel via en trafikskola, optiker eller vårdcentral. Priset varierar.</p>
                            </td>
                            <td align="right">100 kr</td>
                        </tr>
                        <tr>
                            <td class="head_row" colspan="2" style="background-color:#e1e1e1;">
                                <h2><a id="kostnader_trafikskolor" name="kostnader_trafikskolor"></a>Kostnader till trafikskolor</h2>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h3>Lektioner</h3><a id="lektion" name="lektion"></a> Utbildningen ska genomföras hos en behörig utbildare. Utbildningen är minst 12 timmar lång. Av dessa är minst 4 h praktiskt träning där övningskörning i trafik ingår. Resten är teoretisk utbildning. Totalt kan det bli mer än 12 utbildningstimmar beroende på hur du klarar det teoretiska och praktiska målen.
                            </td>
                            <td align="right">Normalt 4000 - 5000 kr</td>
                        </tr>
                    </tbody>
                </table>
            </div><!-- END TAB: am -->
            <!-- START TAB: a -->
            <div class="tab-pane" id="tab-a" role="tabpanel">
                <p>Med ett körkort med behörigheten A får du köra alla tvåhjuliga motorcyklar oavsett slagvolym med motoreffekt</p>
                <table class="table table-bordered margin_top_10">
                    <tbody>
                        <tr>
                            <td class="head_row" colspan="2" style="background-color:#e1e1e1;">
                                <h2>Kostnader för tillstånd och test</h2>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="tillstand" name="tillstand"></a>Körkortstillstånd</h3>
                                <p>Betalas till Transportstyrelsen. Körkortstillstånd efter återkallelse kostar 1670 kr.</p>
                                <p><a href="https://etjanster-kk.transportstyrelsen.se/korkortstillstand/KorkortstillstandAnsokan.aspx" target="_blank" >Ansök om körkortstillstånd</a></p>
                            </td>
                            <td align="right" width="100">150 kr</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="uppskrivning" name="uppskrivning"></a>Uppskrivning (kunskapsprov)</h3>
                                <p>För A1, A2 och A består förarprovet normalt av ett kunskapsprov och ett körprov. Om du har behörigheten A1 eller A2 sedan tidigare, krävs dock bara ett godkänt körprov för att få A2 eller A.</p>
                                <p>Avgiften teoriprov betalas till trafikverket. Vardagar kostar provet 325 kr. Helgdag och vardagar efter kl. 18 kostar det 400 kr.</p>
                            </td>
                            <td align="right">325 kr</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="uppkorning" name="uppkorning"></a>Uppkörning (körprov)</h3>
                                <p>Avgiften teoriprov betalas till trafikverket. Vardagar kostar provet <nobr>1 650 kr</nobr> . Helgdag och efter kl. 18 kostar det <nobr>2 145 kr</nobr> .</p>
                            </td>
                            <td align="right">1 650 kr</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="foto" name="foto"></a>Foto till körkortet</h3>
                                <p>Betalas till Trafikverket. Kortet tas på Förarprovskontoret innan uppskrivningen. Faktura kommer i efterhand.</p>
                            </td>
                            <td align="right">80 kr</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="tillverkning" name="tillverkning"></a>Tillverkning av körkortet</h3>
                                <p>Betalas till Transportstyrelsen.</p>
                            </td>
                            <td align="right">150 kr</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="syntest" name="syntest"></a>Syntest</h3>
                                <p>Görs i regel via en trafikskola, optiker eller vårdcentral. Priset varierar.</p>
                            </td>
                            <td align="right">100 kr</td>
                        </tr>
                        <tr>
                            <td class="head_row" colspan="2" style="background-color:#e1e1e1;">
                                <h2><a id="kostnader_trafikskolor" name="kostnader_trafikskolor"></a>Kostnader till trafikskolor</h2>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="inskrivning" name="inskrivning"></a>Inskrivningsavgift samt material för teorin</h3>
                                <p>- I priset ska tillräckligt mycket litteratur (eller andra medel) ges för att klara uppskrivningen. Körkortsjakten anger priset på Körkortsboken för 350 kr. Körkortsboken är det minsta du behöver införskaffa dig för att klara av teoriprovet.</p>
                                <p>- Det finns inga krav på lärarledda teorilektioner, datorer etc.</p>
                            </td>
                            <td align="right">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="korlektioner" name="korlektioner"></a>Körlektioner</h3>
                                <p>Övningskörning med lärare på trafikskola. I jämförpriset anges 600 minuter som standard i beräkningen. 600 minuter motsvarar 15 körlektioner á 40 minuter vilket är snittet.</p>
                                <p><strong>Kriterierna för att en <em>körlektion</em> ska få vara en del i beräkningen</strong><br>
                                - Körlektionen ska vara 40-80 minuter.<br>
                                - Lektionen ska inte behövas betalas tidigare än samma dag.<br>
                                - Det ska inte vara en del av ett paket där man på förhand måste bestämma antalet lektioner man vill ta.<br>
                                - Priset inkluderar inte extra avgifter för nattkörning.</p>
                            </td>
                            <td align="right">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="riskettan" name="riskettan"></a>Riskutbildning del 1</h3>
                                <p>Den obligatoriska riskutbildningen är speciellt inriktad på motorcykel och omfattar två delar. Innan du gör ditt kunskapsprov och körprov för behörighet A1, A2 och A måste du ha gjort båda delarna i riskutbildningen. Du som har ett giltigt körkort för motorcykel, behöver inte någon ny riskutbildning när du ska göra körprov för en högre motorcykelbehörighet.</p>
                                <p>Riskutbildning för personbil gäller inte för motorcykel.</p>
                                <p>Handlar om alkohol, andra droger, trötthet och riskfyllda beteenden i övrigt.</p>
                            </td>
                            <td align="right">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="halkan" name="halkan"></a>Riskutbildning del 2</h3>
                                <p>handlar om hastighet, säkerhet och körning under särskilda förhållanden.</p>
                            </td>
                            <td align="right">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <h3><a id="korskolan_pris_uppkorning" name="korskolan_pris_uppkorning"></a>Körskolans pris för uppkörningen</h3>
                                <p>Körprovet ska genomföras med en lämplig tvåhjulig motorcykel utan sidvagn. Du måste själv tillhandahålla en sådan motorcykel om du ska använda samma motorcykel under hela provet. I många fall tar körskolan en avgift för att låna ut motorcykel till uppkörning. I priset ingår ofta att man värmer upp lite innan – en kortare körlektion.</p>
                            </td>
                            <td align="right">&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </div><!-- END TAB: a -->
        </div>
    </div>
    <br>
    <h2><a id="synpunkter" name="synpunkter"></a>Synpunkter eller funderingar?<br class="clear"></h2>
    <p>Vänligen <a href="/kontakt">kontakta oss</a> om du har en fråga eller synpunkter på prisjämförelsen.</p>
    <h2><a id="felaktiga_priser" name="felaktiga_priser"></a>Felaktiga priser</h2>
    <p>Att hålla priserna uppdaterade är ett ständigt arbete och ibland blir det inte helt rätt. Vänligen rapportera om ni misstänker <a href="/kontakt">felaktiga priser</a>.<br></p>
    <h2><a id="policy" name="policy"></a>Policy</h2>
    <p>Vår ambition och vision är att vara den naturliga mötesplatsen för kommande elever och trafikskolor. Vårt mål är att lista samtliga trafikskolor med priser och annan relevant information som faller inom kategorin körkort, oavsett om de sponsrar Körkortsjakten på något sätt eller inte. Därigenom kan vi garantera att våra besökare får en heltäckande bild av vad trafikskolor erbjuder och en ge en oberoende prisjämförelse.</p>
    <p>Vi strävar efter att alla priser och annan relevant information ska vara korrekt. Dock kan vi inte garantera att så alltid är fallet. Körkortsjakten tar inte ansvar för eventuella problem som informationen som presenteras kan orsaka. Har du råkat ut för något problem som informationen presenterar är vi tacksamma över om du rapporterar det till oss, så att vi kan rätta till felaktig information.</p>
    <p>Trots samma beteckning så kan erbjudanden för trafikskolor som listas ibland variera något. Det kan t.ex. gälla skolor som erbjuder flera språk, skolor som har nära till kollektivtrafik, medföljande tillbehör mm. Vi uppmanar därför våra besökare att noga studera beskrivningar och annan relevant information, antingen genom trafikskolornas egna webbplatser eller genom direkt kontakt, samt väga in eventuella skillnader vid en prisjämförelse.</p>
    <p>Trots att Körkortsjakten alltid gör sitt yttersta för att informationen ska vara korrekt kan det hända att felaktigheter uppstår. Trafikskolorna hålls inte ansvariga för felaktig information hos Körkortsjakten utan det är alltid den information som återfinns på trafikskolornas webbplatser eller vid direkt kontakt som gäller vid publicering på Körkortsjakten.</p>
    <p>För att underlätta uppdateringar för trafikskolorna kan de själva gå in och redigera sin profil gratis. Dessa uppgifter undersöks av Körkortsjakten och publiceras förutsatt att de är korrekta.</p>
</div>
                    ',
                ],
                'uri' => [
                    'uri' => '/jamforpriset',
                    'status' => \Jakten\Models\PageUri::ACTIVE,
                ],
            ],
            [
                'page' => [
                    'title' => 'Vägen till körkort',
                    'meta_description' => '',
                    'content' => '
<div class="container">
    <h1 class="page-title">Vägen till körkort</h1>
<div role="tabpanel">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs mb-1" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" aria-controls="b" aria-expanded="true" data-toggle="tab" href="#tab-b" role="tab">Bil (B)</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" aria-controls="a" aria-expanded="false" data-toggle="tab" href="#tab-a" role="tab">MC (A)</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" aria-controls="am" aria-expanded="false" data-toggle="tab" href="#tab-am" role="tab">Moped (AM)</a>
            </li>
        </ul><!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane active" id="tab-b" role="tabpanel">
                <div class="row">
                        <div class="col-md-12">
                            <p>
                                <iframe width="100%" height="356" src="https://www.youtube.com/embed/y5zu40q_zaU" frameborder="0" allowfullscreen=""></iframe>
                            </p>

                            <h3>Förberedelser</h3>

                            <div class="card">
                                <div class="card-block">
                                    <h4>Körkortstillstånd</h4>

                                    <p>Innan du kan får börja övningsköra så måste du först och främst ansöka om ett
                                        körkortstillstånd. Ansökan till körkortstillstånd hittar du <a href="http://www.korkortsportalen.se/jag-ska-ta-korkort/B--Personbil-och-latt-lastbil-huvudsida/personbil/Korkortstillstand/" target="_blank" >här</a>.
                                        Du måste komplettera din ansökan med ett synintyg. </p>

                                </div>
                            </div>

                            <div class="card">
                                <div class="card-block">
                                    <h4>Introduktionskurs (Handledarkurs)</h4>

                                    <p>Om du vill kunna övningsköra privat så måste du ha en personlig handledare
                                        (det kan
                                        exempelvis vara någon i familjen). För att kunna bli handledare så måste
                                        personen
                                        ifråga
                                        fyllt 24 år och har körkort för den behörighet som övningskörningen gäller
                                        samt har
                                        haft
                                        körkort i minst 5 år under de senaste 10 åren. Det krävs att ni båda har
                                        gått en
                                        introduktionskurs (handledarkurs).


                                    </p>

                                    <p><a href="/s/kurser/introduktionskurs">
                                            <button type="button" class="btn btn-md btn-success">Boka introduktionskurs
                                            </button>
                                        </a>
                                    </p>

                                    <p>För att du som elev ska kunna gå en introduktionsutbildning så måste du vara
                                        minst 15
                                        år och
                                        9 månader. En godkänd handledare krävs för att kunna övningsköra privat.</p>

                                    <p>Under introduktionsutbildningen får ni bland annat information om vad
                                        handledaren
                                        ansvarar
                                        för, bedömningskriterier vid uppkörningen och uppskrivningen samt om
                                        trafiksäkerhet.
                                        Priset
                                        på introduktionsutbildningen bestäms av anordnaren och kursen pågår i minst
                                        tre
                                        timmar.
                                    </p>
                                </div>
                            </div>

                            <h3>Övning</h3>


                            <div class="card">
                                <div class="card-block">
                                    <h4>Övningsköra</h4>

                                    <p>Övningsköra får du som är 16 år eller äldre göra. Om det är möjligt bör du
                                        köra en
                                        kombination mellan att övningsköra på en trafikskola och övningsköra privat
                                        för att
                                        du ska
                                        lyckas så bra som möjligt. På trafikskolan får du professionell hjälp med
                                        din
                                        körning och
                                        privat så får du din körvana.</p>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-block">
                                    <h4>Teori</h4>

                                    <p>
                                        Teorin bör du läsa parallellt med att du övningskör. Alla körskolor erbjuder
                                        litteratur som bygger på Trafikverkets kursplan. En del erbjuder dessutom
                                        möjligheten att plugga teori på en dator. Det finns även andra hemsidor som
                                        erbjuder
                                        teoritester som är bra att öva på inför det riktiga teoriprovet.

                                    </p>
                                </div>
                            </div>
                            <h3>Riskutbildnignar</h3>


                            <div class="card">
                                <div class="card-block">

                                    <h4>Riskettan (Riskutbildning 1)</h4>

                                    <p>Riskettan är obligatorisk och omfattar lärdom om alkohol, andra droger,
                                        trötthet och
                                        andra riskfyllda beteenden. Utbildningen måste genomföras hos en godkänd
                                        utbildare
                                        (ofta hos en trafikskola), helst mot slutet av din utbildning. Den tar minst
                                        3
                                        timmar och är giltigt i fem år. Priset ligger normalt mellan 400 och 800
                                        kronor. </p>

                                    <p><a href="/s/kurser/riskettan">
                                            <button type="button" class="btn btn-md btn-success">Boka riskettan
                                            </button>
                                        </a>
                                    </p>

                                </div>
                            </div>


                            <div class="card">
                                <div class="card-block">

                                    <h4>Halkan (Riskutbildning 2)</h4>

                                    <p>Halkbana är det andra momentet i riskutbildningarna. En viktig faktor när man
                                        kör bil
                                        är att man har god kontroll över din körning. De flesta tycker att denna
                                        del, är ett
                                        rent nöje. Riskfyllda situationer och körning på halt underlag går
                                        instruktören
                                        genom under de 3-4 timmarna det tar att genomföra utbildningen. Halkan
                                        kostar runt
                                        1300-1800 kronor.</p>
                                </div>
                            </div>


                            <h3>Proven</h3>

                            <div class="card">
                                <div class="card-block">
                                    <h4>Teoriprovet (kunskapsprov)</h4>

                                    <p>Teoriprovet sker på en dator hos Trafikverket. Det innehåller 70 frågor,
                                        varav 5 som
                                        inte räknas med i resultatet. Du behöver svara rätt på 52 av 65, vilket
                                        betyder att
                                        man behöver 80% rätt för att bli godkänd. Provtiden är 50 minuter. Frågorna
                                        handlar
                                        om trafikregler, trafiksäkerhet, fordonkännedom, miljö och personliga
                                        förutsättningar. Ditt godkända kunskapsprov är giltigt i två månader. Både
                                        Kunskapsprovet och Körprovet måste vara godkända under giltighetstiden, om
                                        det gått
                                        mer än två månader måste nya avgifter betalas in och både kunskapsprovet och
                                        körprovet måste göras om.
                                        Läs mer om <a href="http://www.korkortsportalen.se/jag-ska-ta-korkort/B--Personbil-och-latt-lastbil-huvudsida/personbil/Forarprov/Kunskapsprov/Anpassade-prov1/" target="_blank" >anpassade
                                            prov</a> för personer i behov av speciellt stöd eller andra språk.
                                    </p>

                                    <p>
                                        <a class="btn btn-md btn-primary" href="https://fp.trafikverket.se/Boka/#/licence" target="_blank" >Boka teoriprov hos Trafikverket</a>
                                    </p>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-block">

                                    <h4>Uppkörning (körprov)</h4>

                                    <p>
                                        <iframe width="100%" height="319" src="https://www.youtube.com/embed/10F9WRKS768" frameborder="0" allowfullscreen=""></iframe>
                                    </p>

                                    <p>Ett körprov är ca 45 minuter långt och under ditt körprov så ska du visa att
                                        du kan
                                        hantera bilen i trafiken och att du kör på ett bra och säkert sätt. Du
                                        framför under
                                        provet att du har de kunskaper som krävs och du visar att du har kontroll
                                        över din
                                        körning. Om du blir godkänd så är ditt godkännande giltigt i två månader. Om
                                        det
                                        gått mer än två månader och du inte har blivit godkänd på både
                                        kunskapsprovet och
                                        körprovet så måste nya avgifter betalas in och både kunskapsprovet och
                                        körprovet
                                        måste göras om. </p>
                                    <p>
                                        <a class="btn btn-md btn-primary" href="https://fp.trafikverket.se/Boka/#/licence" target="_blank" >Läs mer</a>
                                    </p>
                                </div>
                            </div>
                        </div>


                    </div>
            </div>
            <div class="tab-pane" id="tab-a" role="tabpanel">
                <div class="row">

                        <div class="col-md-12">

                            <h2>Körkortet för MC</h2>

                            <p>Vem vill inte kunna ha möjlighet att känna det energiska ruset som uppstår när man
                                drar iväg på sin motorcykel och verkligen får följa med i svängarna som vägen
                                bildar. Det är många som vill kunna göra detta, och innan det är möjligt så är det
                                först några olika steg man måste gå igenom. Dem kan du läsa om här!</p>

                            <h3>Förberedelser</h3>

                            <div class="card">
                                <div class="card-block">

                                    <h4>Körkortstillstånd</h4>

                                    <p>För att kunna få övningsköra så behöver du först och främst ha ett godkänt
                                        körkortstillstånd. För att du ska få ditt körkortstillstånd så måste du ha
                                        de
                                        kriterier som transportstyrelsen kräver för att man ska kunna få ta körkort.
                                        För att få körkortstillstånd måste du uppfylla en del personliga och
                                        medicinska krav
                                        som transportstyrelsen bestämmer och gör bedömningen om du uppfyller dessa
                                        krav.<br>
                                        Du är permanent bosatt i Sverige eller har studerat här i minst sex månader.<br>
                                        Du har fyllt 24 år. (20 år om du haft A2-körkort i minst två år)<br>
                                        Du har gått riskutbildning.<br>
                                        Du har gjort ett godkänt förarprov (kunskapsprov och körprov).<br>
                                        Du har inget körkort som är utfärdat i någon annan stat inom EES-området.
                                        Ett sådant
                                        körkort går däremot att byta ut mot ett svenskt.<br>
                                        <a class="btn btn-md btn-primary" href="https://etjanster-kk.transportstyrelsen.se/korkortstillstand/KorkortstillstandAnsokan.aspx" target="_blank" >Ansök om körkortstillstånd</a>
                                    </p>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-block">
                                    <h4>Introduktionskurs</h4>

                                    <p>För att få det mesta av körningen så det bra om man kombinera övningskörning
                                        hos en
                                        trafikskola samt att övningsköra privat med en handledare. För att man ska
                                        kunna få
                                        köra privat så måste handledaren ansökt om att få bli elevens handledare och
                                        fått
                                        det godkänt.
                                        Handledaren får sedan följa med eleven på fordonet eller själv köra ett, vad
                                        övningskörningen avser, lämpligt fordon.</p>

                                </div>
                            </div>

                            <h3>Övning</h3>

                            <div class="card">
                                <div class="card-block">

                                    <h4>Övningsköra</h4>

                                    <p>För att man som elev ska få kunna övningsköra så måste man vara 23 år eller
                                        19,5 med
                                        förutsättningar om att man då haft A2 i minst 1,5 år. Du måste självklart ha
                                        ett
                                        beviljat körkortstillstånd och om du vill kunna övningsköra privat, även ha
                                        en
                                        godkänd handledare.
                                        Genom att köra hos en skola så får du lära dig tekniken och du får bra hjälp
                                        och
                                        tips av utbildade lärare och om du också övningskör privat så får du
                                        körvana. Så en
                                        kombination av dem båda är bäst.</p>

                                </div>
                            </div>
                            <div class="card">
                                <div class="card-block">
                                    <h4>Teori</h4>

                                    <p>Under sin körkortsutbildning så är det viktigt att man både fokuserar på både
                                        teori
                                        och praktik. När du läser teori så lär du dig att kör och tänka med omdöme
                                        och även
                                        förstå hur trafiken fungerar. Att kunna hantera situationer som uppstår i
                                        trafiken
                                        får du också från de kunskaper man lär sig när man läser teori.</p>


                                </div>
                            </div>

                            <div class="card">
                                <div class="card-block">
                                    <h4>Riskutbildning</h4>

                                    <p>Riskutbildningen är obligatorisk och är uppdelad i två delar. Du måste ha
                                        genomfört
                                        båda delarna innan du kan köra upp. Det rekommenderas att man går
                                        utbildningen så
                                        nära inpå körprovet och kunskapsprovet som möjligt.</p>

                                </div>
                            </div>
                            <div class="card">
                                <div class="card-block">
                                    <h4>Risk1</h4>

                                    <p>Riskutbildning del 1 handlar om alkohol, andra droger, trötthet och övriga
                                        riskfyllda
                                        beteenden. Under denna utbildning så får man lära sig vikten av att inte
                                        konsumera
                                        alkohol och andra droger när man ska köra och hur viktigt det är att man kör
                                        på en
                                        säker nivå, både för en själv och för andra i trafiken.</p>

                                </div>
                            </div>

                            <div class="card">
                                <div class="card-block">
                                    <h4>Risk2</h4>

                                    <p>Riskutbildningen del 2 fokuserar på hastighet, körning under särskilda
                                        förhållanden
                                        och säkerhet. Du lär dig hantera olika situationer som kan uppstå i trafiken
                                        och du
                                        får själv känna på hur det känns att sitta bakom styret när det sker. Du får
                                        en
                                        större syn och känsla för körningen som hjälper dig att i framtiden lättare
                                        kunna
                                        hantera olika situationer om de uppstår.</p>
                                </div>
                            </div>

                            <h3>Proven</h3>

                            <div class="card">
                                <div class="card-block">
                                    <h4>Teoriprov (Kunskapsprov)</h4>

                                    <p>Under kunskapsprovet ska du visa att du har de teoretiska kunskaper som krävs
                                        för att
                                        få godkänt. Man vill se så att du kommer köra med ett gott omdöme i
                                        trafiken.</p>

                                    <p>Om man har svårigheter att läsa eller skriva eller om man inte förstår
                                        svenska så bra
                                        så har man möjlighet att få göra ett anpassat kunskapsprov i form av
                                        muntligt prov,
                                        prov med längre provtid eller prov med tolk och med teckenspråkstolk.</p>


                                    <p>Avgiften på kunskapsprovet är 325kr. Efter kl 18:00 på vardagar samt på
                                        helger ligger
                                        avgiften på 400 kr.</p>

                                    <p>
                                        <a class="btn btn-md btn-primary" href="http://www.trafikverket.se/Privat/Korkortsprov/" target="_blank" >Boka ditt kunskapsprov</a>
                                    </p>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-block">
                                    <h4>Uppkörning (Körprov)</h4>

                                    <p>Vid din uppkörning så ska du visa att du kan hantera fordonet i trafiken samt
                                        att du
                                        kör med ett gott omdöme och på ett säkert sätt. Man vill se att du har
                                        kontroll över
                                        fordonet och att du har det som krävs för att få köra själv i trafiken
                                        Innan du kan boka och genomföra ditt körprov måste du ha ett giltigt
                                        kunskapsprov.</p>

                                    <p>Avgiften för körprovet är 1650 kr. Efter kl 18.00 på vardagar samt på helger
                                        ligger
                                        avgiften på 2145 kr.</p>

                                    <p><a class="btn btn-md btn-primary" href="http://www.trafikverket.se/Privat/Korkortsprov/" target="_blank" >Boka ditt kunskapsprov</a>
                            </p></div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane" id="tab-am" role="tabpanel">
                <div class="row">

                        <div class="col-md-12">

                            <h2>Körkort för Mopen (AM)</h2>


                            <p>Om du har körkortsbehörighet för AM ger det dig rätt att köra moped klass I,
                                också kallad EU-moped som är ett fordon konstruerad för en hastighet på
                                45km/h.</p>

                            <h3>Krav</h3>


                            <div class="card">
                                <div class="card-block">
                                    <p>De kraven som man behöver uppfylla för att få ta AM-kort är dessa:</p>
                                    <ul>
                                        <li>Du måste ansökt och fått ett godkänt körkortstillstånd.</li>
                                        <li>Vara permanent bosatt i Sverige eller har studerat här i minst sex
                                            månader.
                                        </li>
                                        <li>Gått en utbildning hos en behörig utbildare.</li>
                                        <li>Fyllt 15 år.</li>
                                        <li>Gjort ett kunskapsprov och blivit godkänd.</li>
                                        <li>Du har inget körkort som är utfärdat i någon annan stat inom
                                            EES-området.
                                            Ett sådant körkort går däremot att byta ut mot ett svensk.
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <h3>Körkortstillstånd</h3>


                            <div class="card">
                                <div class="card-block">


                                    <p>För att kunna få övningsköra samt göra förarprovet så behöver man ha ett
                                        giltigt
                                        körkortstillstånd. Vid ansökan så kontrollerar Transportstyrelsen att de
                                        kraven
                                        som de ställer uppfylls.
                                    </p>

                                    <p>
                                        <a class="btn btn-md btn-primary" href="https://etjanster-kk.transportstyrelsen.se/korkortstillstand/KorkortstillstandAnsokan.aspx" target="_blank" >Ansök om tillstånd</a></p>

                                </div>
                            </div>


                            <h3>Utbildningen</h3>

                            <p>När man gör sin utbildning så är det viktigt att man kommer ihåg att teori och
                                praktik är lika viktiga. När du läser teori så lär du dig att köra med gott
                                omdöme och du lär dig att klara av de spelregler som finns i trafiken.
                                Utbildningen är minst 12 timmar lång och genomförs hos en behörig utbildare.
                                Minst fyra av dessa timmar är då praktisk träning och resten av tiden är
                                teoretisk.</p>

                            <div class="card">
                                <div class="card-block">
                                    <h4>Övningsköra</h4>

                                    <p>Kraven för att man ska få kunna övningsköra är att du minst måste vara 14år
                                        och 9
                                        månader. Du måste ha ett godkänt körkortstillstånd och du måste vara
                                        inskriven
                                        hos en utbildare där du också kan övningsköra. Det är nämligen inte tillåtet
                                        att
                                        övningsköra privat.
                                        När den som utbildar dig sedan rapporterar att du har genomgått utbildningen
                                        så
                                        bokar antingen den som utbildat dig eller du själv en tid kunskapsprov.
                                        Efter
                                        att du gjort din AM utbildning så är.</p>

                                </div>
                            </div>
                            <div class="card">
                                <div class="card-block">
                                    <h4>Kunskapsprov</h4>

                                    <p>Under ditt kunskapsprov så ska du visa att du besitter de kunskaper som
                                        behövs,
                                        så att du kan köra med gott omdöme i trafiken. Kunskapsprovet är det enda
                                        provet
                                        du gör för att få ditt AM kort.</p>

                                    <p>Avgiften för ett kunskapsprov är 325 kr. På vardagar efter kl 18.00 och på
                                        helger
                                        är avgiften 400 kr.</p>

                                    <p><a class="btn btn-md btn-primary" href="http://www.trafikverket.se/Privat/Korkortsprov/" target="_blank" >Boka ditt kunskapsprov</a>

                                </p></div>
                            </div>
                            <div class="card">
                                <div class="card-block">
                                    <h4>Anpassade prov</h4>

                                    <p>Om du har svårigheter med att läsa eller skriva, eller om du inte förstår
                                        svenska
                                        så bra, så finns det anpassade kunskapsprov. Du kan få utföra ett muntligt
                                        prov,
                                        ett prov som har längre provtid eller prov med teckenspråk eller med en
                                        tolk.
                                        För att bli godkänd så behöver man få rätt på minst 52 av de 65
                                        frågorna.</p>


                                </div>
                            </div>
                        </div>

                    </div>
            </div>
        </div>
    </div>
 
</div>
                    ',
                ],
                'uri' => [
                    'uri' => '/korkort',
                    'status' => \Jakten\Models\PageUri::ACTIVE,
                ],
            ],
            [
                'page' => [
                    'title' => 'Halkan',
                    'meta_description' => '',
                    'content' => '
<div class="container">
    <h1 class="page-title">Halkan (Riskutbildning del 2)</h1>

    <p>Under riskutbildningen del 2 kommer du under säkra förhållanden uppleva de vanligaste olyckssituationerna som förekommer i trafiken. Körningen är indelad i olika moment så som slalomkörning mellan koner, bromsningsövningar på både halt och torrt underlag och sväng i hal kurva Se till att boka riskutbildningen i tid.</p>
</div>
                    ',
                ],
                'uri' => [
                    'uri' => '/halkan',
                    'status' => \Jakten\Models\PageUri::ACTIVE,
                ],
            ]

        ];

        foreach ($pages as $page) {
            $uri = $page['uri'];
            unset($page['uri']);
            $pageId = \DB::table('pages')->insertGetId($page['page']);
            $uri['page_id'] = $pageId;
            \DB::table('pages_uris')->insert($uri);
        }
    }
}
