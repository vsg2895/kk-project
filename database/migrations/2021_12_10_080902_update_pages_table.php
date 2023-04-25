<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Jakten\Models\Page;

class UpdatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $faq = Page::query()->where('id', '=', 6)->first();

        $faq->content = '
                        <div class="container">
                <h1 class="page-title">FAQ</h1>
       
                <h2 class="page-title">Kurser</h2>
                <div class="accordion">
                    <div class="accordion__item">
                        <div class="accordion__title">
                            <div class="accordion__arrow"><span class="accordion__arrow-item ">+</span></div>
                            <span class="accordion__title-text">Hur hittar jag enkelt en introduktionskurs?</span>
                        </div>
                        <div class="accordion__content">
                            Genom att välja kurstyp och stad i menyn listas tillgängliga tider för dig. Sedan kan du kan själv välja
                            vilken kurs som passar dig bäst. Hitta introduktionskurser här:
                            https://korkortsjakten.se/introduktionskurser
                        </div>
                    </div>
                    <div class="accordion__item">
                        <div class="accordion__title">
                            <div class="accordion__arrow"><span class="accordion__arrow-item ">+</span></div>
                            <span class="accordion__title-text">Hur hittar jag risk1? </span>
                        </div>
                        <div class="accordion__content">
                            Här hittar du lediga tider för Risk1 https://korkortsjakten.se/riskettan
                        </div>
                    </div>
            
                    <div class="accordion__item">
                        <div class="accordion__title">
                            <div class="accordion__arrow"><span class="accordion__arrow-item ">+</span></div>
                            <span class="accordion__title-text">Hur hittar jag risk2?</span>
                        </div>
                        <div class="accordion__content">
                            Här hittar du lediga tider för Risk2 https://korkortsjakten.se/risktvaan
                        </div>
                    </div>
                    <div class="accordion__item">
                        <div class="accordion__title">
                            <div class="accordion__arrow"><span class="accordion__arrow-item ">+</span></div>
                            <span class="accordion__title-text">Finns introduktionskurs / handledarkurs på andra språk?</span>
                        </div>
                        <div class="accordion__content">
                            Det finns några trafikskolor som erbjuder andra språk. Här kan du hitta introduktionskurs på engelska: https://korkortsjakten.se/kurser/Introduktionskursenglish/all
                        </div>
                    </div>
                    <div class="accordion__item">
                        <div class="accordion__title">
                            <div class="accordion__arrow"><span class="accordion__arrow-item ">+</span></div>
                            <span class="accordion__title-text">Finns Risk1 på andra språk?</span>
                        </div>
                        <div class="accordion__content">
                            Det finns några trafikskolor som erbjuder andra språk. Här kan du hitta Risk1 på engelska: https://korkortsjakten.se/engelskriskettan
                        </div>
                    </div>
                    <div class="accordion__item">
                        <div class="accordion__title">
                            <div class="accordion__arrow"><span class="accordion__arrow-item ">+</span></div>
                            <span class="accordion__title-text">Har nu moped AM paket? </span>
                        </div>
                        <div class="accordion__content">
                            För att boka in ett Moped AM paket körkort tryck här: https://korkortsjakten.se/kurser/mopedam
                        </div>
                    </div>
                    <div class="accordion__item">
                        <div class="accordion__title">
                            <div class="accordion__arrow"><span class="accordion__arrow-item ">+</span></div>
                            <span class="accordion__title-text">Körkortsteori och Testprov</span>
                        </div>
                        <div class="accordion__content">
                            <p>På Körkortsjakten kan du få tillgång till alla körkortsfrågor du behöver kunna för att klara teoriprovet. Alla körkortsfrågor har en förklaring som snabbt hjälper dig att förstå även det svåraste frågorna.
                            Köp här: https://korkortsjakten.se/kurser/teoriprov-online
                                Du studerar och testar dig när du vill med obegränsat antal prov. Körkortsfrågorna och proven fungerar på alla enheter, både dator och mobil. Samtliga frågor och prov finns på svenska alternativt engelska.</p>

                            <p>Du kan köpa hela paketet där du får tillgång till över 1300 körkortsfrågor och obegränsat antal prov, som du behöver för att klara teoriprovet. Giltigt i 6 månader. Alternativt, om du känner dig osäker på en
                                specifik del/ämne inom teorin, så kan du endast köpa den delen och få tillgång till ca. 250-300 frågor och obegränsat antal prov inom det ämnet. Giltigt i 3 månader.
                            </p>

                            <ul>
                                <li>Fordonskännedom, Personlig förutsättningar och Miljö</li>
                                <li>Stadskörning, Landsvägskörning och Motorvägskörning</li>
                                <li>Trafiksäkerhetg</li>
                                <li>Trafikregler</li>
                            </ul>
           
                        </div>
                    </div>
                </div>
            
                <br>
                <h2 class="page-title">Bokning & Betalning </h2>
                <div class="accordion">
                    <div class="accordion__item">
                        <div class="accordion__title">
                            <div class="accordion__arrow"><span class="accordion__arrow-item ">+</span></div>
                            <span class="accordion__title-text">Hur bokar jag en kurs på Körkortsjakten?</span>
                        </div>
                        <div class="accordion__content">
                            När du har hittat den kurs som passar dig bäst följer du stegen för att fylla i dina uppgifter och betala online. Lägg kursen i varukorgen och gå till betala. Du kan sedan välja att att betala mot faktura (0 kr 14 dagar från behandlingsdagen), kort, banköverföring, Swish eller delbetalning med Klarna. Observera att det inte går att boka utan betala.
                        </div>
                    </div>
                    <div class="accordion__item">
                        <div class="accordion__title">
                            <div class="accordion__arrow"><span class="accordion__arrow-item ">+</span></div>
                            <span class="accordion__title-text">Ska jag betala igen på trafikskolan?</span>
                        </div>
                        <div class="accordion__content">
                            Nej , du ska inte betala något på trafikskolan. Detta gäller oavsett vilket betalsätt du valde på Klarna.
                        </div>
                    </div>
                    <div class="accordion__item">
                        <div class="accordion__title">
                            <div class="accordion__arrow"><span class="accordion__arrow-item ">+</span></div>
                            <span class="accordion__title-text">Hur ombokar / avbokar jag jag en kurs på Körkortsjakten?</span>
                        </div>
                        <div class="accordion__content">
                            Om du önskar omboka eller avboka en bokad kurs ska detta ske senast 48h innan kursens starttid. Du loggar in med användarnamn (din epostadress) och ditt lösenord här:https://korkortsjakten.se/logga-in för att adminsterera dina bokningar. Om du väljer att omboka kursen betalas hela beloppet tillbaka som ett saldo på ditt konto och du kan sedan när du vill köpa en ny kurs på vilken trafikskola du önskar. Om du avbokar kursen i sin helhet får du en återbetalning på samma sätt som du betalade på. Bokningsavgiften på 39kr är då inte återbetalningsbar.
                        </div>
                    </div>
                    <div class="accordion__item">
                        <div class="accordion__title">
                            <div class="accordion__arrow"><span class="accordion__arrow-item ">+</span></div>
                            <span class="accordion__title-text">Hur betalar jag en kurs ?</span>
                        </div>
                        <div class="accordion__content">
                            Du kan endast fullfölja din bokning genom att betala. Du kan välja att betala mot faktura (0 kr 14 dagar från behandlingsdagen), kort, banköverföring ,swish eller delbetalning.
                        </div>
                    </div>
                    <div class="accordion__item">
                        <div class="accordion__title">
                            <div class="accordion__arrow"><span class="accordion__arrow-item ">+</span></div>
                            <span class="accordion__title-text">Jag har inga pengar just nu, vad kan jag göra?</span>
                        </div>
                        <div class="accordion__content">
                            Körkortsjakten erbjuder Klarna-faktura: Du kan boka innan lön och betala 14 dagar efter bokning utan fakturaavgift.
                        </div>
                    </div>
                    <div class="accordion__item">
                        <div class="accordion__title">
                            <div class="accordion__arrow"><span class="accordion__arrow-item ">+</span></div>
                            <span class="accordion__title-text">Erbjuder ni delbetalning?</span>
                        </div>
                        <div class="accordion__content">
                            Ja, du kan välja delbetalning med Klarna i kassan när du betalar.
                        </div>
                    </div>
                    <div class="accordion__item">
                        <div class="accordion__title">
                            <div class="accordion__arrow"><span class="accordion__arrow-item ">+</span></div>
                            <span class="accordion__title-text">Hur går det till när jag köper ett paket?</span>
                        </div>
                        <div class="accordion__content">
                            Du väljer ett paket på den trafikskola som passar dig. Paketen är ofta generöst rabatterade och det är då en överenskommelse mellan trafikskolan och dig som elev vad paketet ska innehålla. När bokningen är bekräftad kommer trafikskolan höra av sig till dig för att ni tillsammans ska planera din utbildning.
                        </div>
                    </div>
                </div>
            
                <br>
                <h2 class="page-title">Kontohantering, glömt lösenord</h2>
                <div class="accordion">
                    <div class="accordion__item">
                        <div class="accordion__title">
                            <div class="accordion__arrow"><span class="accordion__arrow-item ">+</span></div>
                            <span class="accordion__title-text">Hur loggar jag in på Körkortsjakten?</span>
                        </div>
                        <div class="accordion__content">
                            Du loggar in med användarnamn (din epostadress) och ditt lösenord här:https://korkortsjakten.se/logga-in
                        </div>
                    </div>
                    <div class="accordion__item">
                        <div class="accordion__title">
                            <div class="accordion__arrow"><span class="accordion__arrow-item ">+</span></div>
                            <span class="accordion__title-text">Vad händer om jag har glömt mitt lösenord?</span>
                        </div>
                        <div class="accordion__content">
                            Om du har glömt ditt lösenord till Körkortsjakten så återställer du enkelt ditt lösenord genom att klicka på länken "Glömt lösenordet?" här: https://korkortsjakten.se/losenord/glomt
                        </div>
                    </div>
                    <div class="accordion__item">
                        <div class="accordion__title">
                            <div class="accordion__arrow"><span class="accordion__arrow-item ">+</span></div>
                            <span class="accordion__title-text">Hur skapar jag ett konto?</span>
                        </div>
                        <div class="accordion__content">
                            Du skapar ett konto hos Körkortsjakten enklast genom att besöka https://korkortsjakten.se/registrera/anvandare . Fyll i dina kontouppgifter samt läs igenom & godkänn Användarvillkoren. Observera att när du gör en beställning första gånge skapas ett konto automatiskt.
                        </div>
                    </div>
                </div>
            
                <br>
                <h2 class="page-title">Kundtjänst</h2>
                <div class="accordion">
                    <div class="accordion__item">
                        <div class="accordion__title">
                            <div class="accordion__arrow"><span class="accordion__arrow-item ">+</span></div>
                            <span class="accordion__title-text">Jag har ett problem, hur kontaktar jag kundservice?</span>
                        </div>
                        <div class="accordion__content">
                            Du når enklast vår kundservice genom att skicka epost till kontakt@korkortsjakten.se
                        </div>
                    </div>
                </div>
            </div>
        ';

        $faq->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        $faq = Page::query()->where('id', '=', 6)->first();

        $faq->content = '<div class="container">
                    <h1 class="page-title">FAQ</h1>
                
                 <h3>Vanliga frågor från elever</h3>
                
                 <h3>1. Hur går det till vid en beställning hos Körkortsjakten?</h3>
                    <p>När du valt din produkt (kurs, paket eller vara) bekräftar du beställningen genom betalning i vår checkout där du har tillgång till Klarnas olika betalsätt. När köpet är genomfört får du omgående ett bekräftelsemejl på din bokning, och likaså får trafikskolan ett mejl med din bokning. Det kommer inget separat bekräftelsemejl från trafikskolan. Ha din bekräftelse tillgänglig att visa upp på trafikskolan när du kommer för att ta din lektion eller hämta ut din vara.</p>
                
                 <h3>2. Hur går betalningen efter en beställning hos Körkortsjakten till?</h3>
                    <p>Så snart du bekräftat din beställning genom att genomföra köpet i kassan genereras en faktura/betalning för körkortsjaktens bokningsavgift. Faktura/underlag för bokningsavgift kommer separat ifrån Klarna. Fakturan/betalningen för beställningar innehållande kurser genereras 24 h innan kurstillfället. Faktura/betalning för beställningar innehållande körpaket genereras när du och trafikskolan bokat in dig i deras system, i annat fall senast 14 dagar efter beställningstillfället</p>
                
                 <br>
                
                 <h3>Vanliga frågor från trafikskolor</h3>
                
                    <h3>1. Hur gör jag för att lägga upp min skola på Körkortsjakten?</h3>
                    <p>Att få en profil med sina prisuppgifter och kontaktuppgifter på Körkortsjakten är kostnadsfritt och enkelt. Vår ambition och vision är att vara den naturliga mötesplatsen för kommande elever och trafikskolor. Vårt mål är att lista samtliga trafikskolor med priser och annan relevant information som faller inom kategorin körkort, oavsett om de sponsrar Körkortsjakten på något sätt eller inte. Därigenom kan vi garantera att våra besökare får en heltäckande bild av vad trafikskolor erbjuder och en ge en oberoende prisjämförelse.</p>
                
                
                    <h3>2. Hur gör jag för att rapportera felaktiga priser på Körkortsjakten?</h3>
                    <p>Körkortsjakten gör alltid gör sitt yttersta för att informationen ska vara korrekt men det kan hända att felaktigheter uppstår. Trafikskolorna hålls inte ansvariga för felaktig information hos Körkortsjakten utan det är alltid den information som återfinns på trafikskolornas webbplatser eller vid direkt kontakt som gäller vid publicering på Körkortsjakten. För att underlätta uppdateringar för trafikskolorna kan de själva gå in och redigera sin profil eller genom att maila till kontakt@korkortsjakten.se Dessa uppgifter undersöks av Körkortsjakten och publiceras förutsatt att de är korrekta.</p>
                
                
                    <h3>3. Hur gör jag för att lägga upp mina kurstillfällen på Körkortsjakten?</h3>
                    <p>Att lägga upp tider är kostnadsfritt för trafikskolor och det finns ingen bindningstid. Ni kan när som helst lägga upp tider hos oss och när som helst ta bort dem. Trafikskolan behöver ansluta sig till körkortsjakten.se genom att registrera sig, eller "claima" trafikskolan, som oftast finns listad i vårt register redan. Då skapas ett administrationskonto där trafikskolan själv administrerar sitt utbud och priser, och trafikskolan blir kostnadsfritt ansluten till Körkortsjaktens ButiksID hos Klarna, som tillhandahåller den betallösning körkortsjakten.se erbjuder bokare. Körkortsjaktens kundtjänst erbjuder alltid servicen att hjälpa till med att lägga upp kurser löpande</p>
                
                
                    <h3>4. Vad behöver ni ha för uppgifter för att jag ska kunna lägga upp mina tider för handledarkurs/introduktionskurs?</h3>
                    <p>Pris, datum, tid, längd på kursen, betalningssätt för eleven. Skicka in till kontakt@kortkortsjakten.se</p>
                
                
                    <h3>5. Hur betalar eleven kursen till mig?</h3>
                    <p>Genom att du ansluter din trafikskola till körkortsjakten ansluts du kostnadsfritt till Klarnas betallösning. Affären är sedan mellan eleven/bokaren och Klarna och du är därmed garanterad din intäkt. Utbetalning för dina intäkter sker från Körkortsjakten och du slipper all administration kring fakturering, påminnelser m.m. Vid utbetalning är Klarnas transaktionsavgift på 1,8% samt Körkortsjaktens provision avdragen</p>
                
                
                    <h3>6. Hur vet jag om en kurs får bokningar via körkortsjakten?</h3>
                    <p>Du får en bokningsbekräftelse med de uppgifter du behöver för att boka in eleven/eleverna i ditt eget system. Genom din adminstratörsinloggning kan du justera antalet platser som ligger bokningsbara via körkortsjakten utifrån det antal bokningar du får in på egen hand, så inte kurserna riskerar bli överfulla.</p>
                
                
                    <h3>7. Hur mycket satsar Körkortsjakten på Marknadsföring?</h3>
                    <p>Marknadsföringen på internet är en av de absolut viktigaste marknadsföringskanalerna. Körkortsjakten har stor erfarenhet av onlinemarknadsföring och om vad som fungerar i olika typer av digitala medier. Vi vet vad som gör en hemsida framgångsrik och användbar. Vi arbetar dagligen med SEO, googleoptimering och kommunikation i sociala medier, vilket ger anslutna Trafikskolor en framskjuten position på nätet. Körkortsjakten finns alltid i samma rum där våra besökare finns. Genom att trafikskolan syns på Körkortsjakten kommer den automatiskt alltid att synas i våra sammanhang för kommande elever.</p>
                
                
                    <h3>8. Hur gör jag om jag vill annonsera på Körkortsjakten?</h3>
                    <p>Om du är intresserad av att annonsera på Körkortsjakten hör av dig till oss på kontakt@korkortsjakten.se</p>
                
                
                    <h3>9. Hur kan min trafikskola hamna högre upp på Körkortsjakten?</h3>
                    <p>Körkortsjakten är en oberoende prisjämförelse. Din trafikskola kan inte hamna högre upp om trafikskolan inte sänker priserna. En trafikskola kan dock förbättra sina chanser att synas mer på Körkortsjakten. Är du intresserad på att veta hur? Maila kontakt@korkortsjakten.se</p>
                    
                    <hr class="section-divider">
                    
                    <p>Har du frågor kring utbetalningar, orderhanteringar eller fakturor? Kontakta oss på kontakt@kortkortsjakten.se</a>
                </div>

        ';

        $faq->save();
    }
}
