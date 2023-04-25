<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Jakten\Models\Page;
use Illuminate\Support\Facades\DB;

class AddNewVehicleTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('vehicles')->insert([
            'id' => 4,
            'name' => 'YKB',
            'label' => 'vehicles.ykb',
        ]);

        $segments = [
            [
                'name' => 'YKB_FORTUTBILDNING_35_H',
                'title' => 'YKB UTBILDNING GRUNDKURS 35 H',
                'vehicle_id' => 4,
                'default_price' => 0,
                'comparable' => true,
                'bookable' => true,
                'sub_description' => '<b>De fem momenten är: </b>  <br/>  YKB Delkurs 1 Sparsam Körning      <br/>YKB Delkurs 2 Godstransporter   <br/>   YKB Delkurs 3 Lagar och Regler <br/> YKB Delkurs 4 Ergonomi och Hälsa <br/>  YKBDelkurs 5  Trafiksäkerhet och Kundfokus <br/><br/>   <b>Här kan du boka samtliga delar. </b>',
                'description' => 'Varje yrkesförare måste genomgå en fortbildningskurs vart femte år på minst 35 timmar, fördelat på 5 olika moment. <b>Boka och betala med Klarna Online. </b>',
                'editable' => true,
                'sub_explanation' => 'För varje delmoment man genomfört utfärdas ett intyg. Utbildningsanordnaren anmäler till Transportstyrelsen när sista delmomentet är genomfört. Man har sedan ytterligare 5 år på sig att genomföra en ny fortbildning. Det krävs inget prov på Trafikverket för fortbildningen.',
                'explanation' => null,
                'order' => 10,
                'admin_only' => 1,
                'slug' => 'ykb_35_h'
            ],
            [
                'name' => 'YKB_GRUNDKURS_140_H',
                'title' => 'YKB UTBILDNING GRUNDKURS 140 H',
                'vehicle_id' => 4,
                'default_price' => 0,
                'comparable' => true,
                'bookable' => true,
                'sub_description' => 'YKB utbildning är ett lagkrav för chaufförer som kör lastbil alt. buss i yrkestrafik. - Boka och betala med Klarna Online',
                'description' => 'För att få utföra gods- eller persontransporter med tung lastbil eller buss krävs att föraren har ett yrkeskompetenbevis utöver körkort för fordonsslaget. Yrkeskompetensbevis får man när man har genomgått en grundutbildning. Utbildningen ska avslutas med ett godkänt skriftligt prov. Grundutbildningen är 140 timmar. Den förkortade grundutbildningen ska omfatta minst 10 timmars praktisk utbildnig. Provet avläggs oftast vid Trafikverkets förarprovskontor. Den kortare grundutbildningen, som är på 140 timmar, är avsedd för dig som är 23 år* och äldre och utbildar dig till förare för persontransporter med buss eller för dig som är 21 år och äldre och utbildar dig till förare för godstransporter med tung lastbil. Innan du påbörjar din utbildning bör du ha ditt C alt. D- kort klart eller nästan klart. Detta för att få din bästa möjliga utbildning.',
                'editable' => true,
                'sub_explanation' => 'YKB UTBILDNING GRUNDKURS 140 H

Vart femte år behöver YKB beviset uppdateras med fortbildning för transportslagen för att få sitt yrkeskompetensbevis förnyat.
',
                'explanation' => null,
                'order' => 10,
                'admin_only' => 0,
                'slug' => 'ykb_140_h'
            ],
            [
                'name' => 'YKB_35_1',
                'title' => 'YKB Delkurs 1 Sparsam Körning',
                'vehicle_id' => 4,
                'default_price' => 0,
                'comparable' => true,
                'bookable' => true,
                'sub_description' => '<b>De fem momenten är: </b>  <br/>  YKB Delkurs 1 Sparsam Körning      <br/>   YKB Fortutbildning 35 h) YKB Delkurs 2 Godstransporter   <br/>    YKB Fortutbildning 35 h) YKB Delkurs 3 Lagar och Regler <br/> YKB Fortutbildning 35 h) YKB Delkurs 4 Ergonomi och Hälsa <br/>  YKBDelkurs 5  Trafiksäkerhet och Kundfokus <br/>   Här kan du boka samtliga delar.',
                'description' => 'Varje yrkesförare måste genomgå en fortbildningskurs vart femte år på minst 35 timmar, fördelat på 5 olika moment. <b>Boka och betala med Klarna Online. </b>',
                'editable' => true,
                'sub_explanation' => 'För varje delmoment man genomfört utfärdas ett intyg. Utbildningsanordnaren anmäler till Transportstyrelsen när sista delmomentet är genomfört. Man har sedan ytterligare 5 år på sig att genomföra en ny fortbildning. Det krävs inget prov på Trafikverket för fortbildningen.',
                'explanation' => null,
                'order' => 10,
                'admin_only' => 1,
                'slug' => null
            ],
            [
                'name' => 'YKB_35_2',
                'title' => 'YKB Delkurs 2 Godstransporter',
                'vehicle_id' => 4,
                'default_price' => 0,
                'comparable' => true,
                'bookable' => true,
                'sub_description' => '<b>De fem momenten är: </b>  <br/>  YKB Delkurs 1 Sparsam Körning      <br/>   YKB Fortutbildning 35 h) YKB Delkurs 2 Godstransporter   <br/>    YKB Fortutbildning 35 h) YKB Delkurs 3 Lagar och Regler <br/> YKB Fortutbildning 35 h) YKB Delkurs 4 Ergonomi och Hälsa <br/>  YKBDelkurs 5  Trafiksäkerhet och Kundfokus <br/>   Här kan du boka samtliga delar.',
                'description' => 'Varje yrkesförare måste genomgå en fortbildningskurs vart femte år på minst 35 timmar, fördelat på 5 olika moment. <b>Boka och betala med Klarna Online. </b>',
                'editable' => true,
                'sub_explanation' => 'För varje delmoment man genomfört utfärdas ett intyg. Utbildningsanordnaren anmäler till Transportstyrelsen när sista delmomentet är genomfört. Man har sedan ytterligare 5 år på sig att genomföra en ny fortbildning. Det krävs inget prov på Trafikverket för fortbildningen.',
                'explanation' => null,
                'order' => 10,
                'admin_only' => 1,
                'slug' => null
            ],
            [
                'name' => 'YKB_35_3',
                'title' => 'YKB Delkurs 3 Lagar och Regler',
                'vehicle_id' => 4,
                'default_price' => 0,
                'comparable' => true,
                'bookable' => true,
                'sub_description' => '<b>De fem momenten är: </b>  <br/>  YKB Delkurs 1 Sparsam Körning      <br/>   YKB Fortutbildning 35 h) YKB Delkurs 2 Godstransporter   <br/>    YKB Fortutbildning 35 h) YKB Delkurs 3 Lagar och Regler <br/> YKB Fortutbildning 35 h) YKB Delkurs 4 Ergonomi och Hälsa <br/>  YKBDelkurs 5  Trafiksäkerhet och Kundfokus <br/>   Här kan du boka samtliga delar.',
                'description' => 'Varje yrkesförare måste genomgå en fortbildningskurs vart femte år på minst 35 timmar, fördelat på 5 olika moment. <b>Boka och betala med Klarna Online. </b>',
                'editable' => true,
                'sub_explanation' => 'För varje delmoment man genomfört utfärdas ett intyg. Utbildningsanordnaren anmäler till Transportstyrelsen när sista delmomentet är genomfört. Man har sedan ytterligare 5 år på sig att genomföra en ny fortbildning. Det krävs inget prov på Trafikverket för fortbildningen.',
                'explanation' => null,
                'order' => 10,
                'admin_only' => 1,
                'slug' => null
            ],
            [
                'name' => 'YKB_35_4',
                'title' => 'YKB Delkurs 4 Ergonomi och Hälsa',
                'vehicle_id' => 4,
                'default_price' => 0,
                'comparable' => true,
                'bookable' => true,
                'sub_description' => '<b>De fem momenten är: </b>  <br/>  YKB Delkurs 1 Sparsam Körning      <br/>   YKB Fortutbildning 35 h) YKB Delkurs 2 Godstransporter   <br/>    YKB Fortutbildning 35 h) YKB Delkurs 3 Lagar och Regler <br/> YKB Fortutbildning 35 h) YKB Delkurs 4 Ergonomi och Hälsa <br/>  YKBDelkurs 5  Trafiksäkerhet och Kundfokus <br/>   Här kan du boka samtliga delar.',
                'description' => 'Varje yrkesförare måste genomgå en fortbildningskurs vart femte år på minst 35 timmar, fördelat på 5 olika moment. <b>Boka och betala med Klarna Online. </b>',
                'editable' => true,
                'sub_explanation' => 'För varje delmoment man genomfört utfärdas ett intyg. Utbildningsanordnaren anmäler till Transportstyrelsen när sista delmomentet är genomfört. Man har sedan ytterligare 5 år på sig att genomföra en ny fortbildning. Det krävs inget prov på Trafikverket för fortbildningen.',
                'explanation' => null,
                'order' => 10,
                'admin_only' => 1,
                'slug' => null
            ],
            [
                'name' => 'YKB_35_5',
                'title' => 'YKB Delkurs 5  Trafiksäkerhet och Kundfokus',
                'vehicle_id' => 4,
                'default_price' => 0,
                'comparable' => true,
                'bookable' => true,
                'sub_description' => '<b>De fem momenten är: </b>  <br/>  YKB Delkurs 1 Sparsam Körning      <br/>   YKB Fortutbildning 35 h) YKB Delkurs 2 Godstransporter   <br/>    YKB Fortutbildning 35 h) YKB Delkurs 3 Lagar och Regler <br/> YKB Fortutbildning 35 h) YKB Delkurs 4 Ergonomi och Hälsa <br/>  YKBDelkurs 5  Trafiksäkerhet och Kundfokus <br/>   Här kan du boka samtliga delar.',
                'description' => 'Varje yrkesförare måste genomgå en fortbildningskurs vart femte år på minst 35 timmar, fördelat på 5 olika moment. <b>Boka och betala med Klarna Online. </b>',
                'editable' => true,
                'sub_explanation' => 'För varje delmoment man genomfört utfärdas ett intyg. Utbildningsanordnaren anmäler till Transportstyrelsen när sista delmomentet är genomfört. Man har sedan ytterligare 5 år på sig att genomföra en ny fortbildning. Det krävs inget prov på Trafikverket för fortbildningen.',
                'explanation' => null,
                'order' => 10,
                'admin_only' => 1,
                'slug' => null
            ]
        ];

        DB::table('vehicle_segments')->insert($segments);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
