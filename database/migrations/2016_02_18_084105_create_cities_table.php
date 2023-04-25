<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('county_id')->unsigned();
            $table->foreign('county_id')->references('id')->on('counties');
            $table->string('name');
            $table->string('slug');
            $table->double('latitude');
            $table->double('longitude');
            $table->timestamps();
        });

        DB::table('cities')->insert($this->getCities());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropForeign('cities_county_id_foreign');
        });
        DB::table('cities')->delete();
        Schema::dropIfExists('cities');
    }

    protected function getCities()
    {
        return [
            ['id' => 10000, 'county_id' => 8, 'name' => 'Uppgift saknas', 'slug' => 'null', 'latitude' => 56.630686, 'longitude' => 15.540006],
            ['id' => 1, 'county_id' => 8, 'name' => 'Emmaboda', 'slug' => 'emmaboda', 'latitude' => 56.630686, 'longitude' => 15.540006],
            ['id' => 2, 'county_id' => 8, 'name' => 'Gamleby', 'slug' => 'gamleby', 'latitude' => 57.89316, 'longitude' => 16.406089],
            ['id' => 3, 'county_id' => 8, 'name' => 'Hultsfred', 'slug' => 'hultsfred', 'latitude' => 57.49484, 'longitude' => 15.841651],
            ['id' => 4, 'county_id' => 8, 'name' => 'Kalmar', 'slug' => 'kalmar', 'latitude' => 56.663445, 'longitude' => 16.356779],
            ['id' => 5, 'county_id' => 8, 'name' => 'Mönsterås', 'slug' => 'monsteras', 'latitude' => 57.04158, 'longitude' => 16.44312],
            ['id' => 6, 'county_id' => 8, 'name' => 'Nybro', 'slug' => 'nybro', 'latitude' => 56.7438, 'longitude' => 15.908681],
            ['id' => 7, 'county_id' => 8, 'name' => 'Oskarshamn', 'slug' => 'oskarshamn', 'latitude' => 57.265699, 'longitude' => 16.447398],
            ['id' => 8, 'county_id' => 8, 'name' => 'Torsås', 'slug' => 'torsas', 'latitude' => 56.412638, 'longitude' => 15.998275],
            ['id' => 9, 'county_id' => 8, 'name' => 'Vimmerby', 'slug' => 'vimmerby', 'latitude' => 57.669034, 'longitude' => 15.858857],
            ['id' => 10, 'county_id' => 8, 'name' => 'Västervik', 'slug' => 'vastervik', 'latitude' => 57.757716, 'longitude' => 16.636976],
            ['id' => 12, 'county_id' => 1, 'name' => 'Karlshamn', 'slug' => 'karlshamn', 'latitude' => 56.170303, 'longitude' => 14.863073],
            ['id' => 13, 'county_id' => 1, 'name' => 'Karlskrona', 'slug' => 'karlskrona', 'latitude' => 56.161224, 'longitude' => 15.5869],
            ['id' => 14, 'county_id' => 1, 'name' => 'Olofström', 'slug' => 'olofstrom', 'latitude' => 56.277708, 'longitude' => 14.530938],
            ['id' => 15, 'county_id' => 1, 'name' => 'Ronneby', 'slug' => 'ronneby', 'latitude' => 56.210434, 'longitude' => 15.276023],
            ['id' => 16, 'county_id' => 1, 'name' => 'Sölvesborg', 'slug' => 'solvesborg', 'latitude' => 56.053743, 'longitude' => 14.579688],
            ['id' => 17, 'county_id' => 2, 'name' => 'Avesta', 'slug' => 'avesta', 'latitude' => 60.14533, 'longitude' => 16.17384],
            ['id' => 18, 'county_id' => 2, 'name' => 'Borlänge', 'slug' => 'borlange', 'latitude' => 60.484304, 'longitude' => 15.433969],
            ['id' => 19, 'county_id' => 2, 'name' => 'Falun', 'slug' => 'falun', 'latitude' => 60.60646, 'longitude' => 15.6355],
            ['id' => 20, 'county_id' => 2, 'name' => 'Hedemora', 'slug' => 'hedemora', 'latitude' => 60.277545, 'longitude' => 15.985892],
            ['id' => 21, 'county_id' => 2, 'name' => 'Leksand', 'slug' => 'leksand', 'latitude' => 60.730308, 'longitude' => 14.999892],
            ['id' => 22, 'county_id' => 2, 'name' => 'Ludvika', 'slug' => 'ludvika', 'latitude' => 60.152358, 'longitude' => 15.191639],
            ['id' => 23, 'county_id' => 2, 'name' => 'Malung', 'slug' => 'malung', 'latitude' => 60.686372, 'longitude' => 13.720966],
            ['id' => 24, 'county_id' => 2, 'name' => 'Mora', 'slug' => 'mora', 'latitude' => 61.004878, 'longitude' => 14.537003],
            ['id' => 25, 'county_id' => 2, 'name' => 'Orsa', 'slug' => 'orsa', 'latitude' => 61.116937, 'longitude' => 14.628071],
            ['id' => 26, 'county_id' => 2, 'name' => 'Rättvik', 'slug' => 'rattvik', 'latitude' => 60.889025, 'longitude' => 15.123373],
            ['id' => 27, 'county_id' => 2, 'name' => 'Smedjebacken', 'slug' => 'smedjebacken', 'latitude' => 60.143193, 'longitude' => 15.415992],
            ['id' => 28, 'county_id' => 2, 'name' => 'Säter', 'slug' => 'sater', 'latitude' => 60.34665, 'longitude' => 15.7479],
            ['id' => 29, 'county_id' => 2, 'name' => 'Vansbro', 'slug' => 'vansbro', 'latitude' => 60.509943, 'longitude' => 14.225342],
            ['id' => 30, 'county_id' => 3, 'name' => 'Visby', 'slug' => 'visby', 'latitude' => 57.6348, 'longitude' => 18.29484],
            ['id' => 31, 'county_id' => 4, 'name' => 'Bollnäs', 'slug' => 'bollnas', 'latitude' => 61.34838, 'longitude' => 16.394268],
            ['id' => 32, 'county_id' => 4, 'name' => 'Gävle', 'slug' => 'gavle', 'latitude' => 60.67488, 'longitude' => 17.141273],
            ['id' => 33, 'county_id' => 4, 'name' => 'Hudiksvall', 'slug' => 'hudiksvall', 'latitude' => 61.727391, 'longitude' => 17.107401],
            ['id' => 34, 'county_id' => 4, 'name' => 'Ljusdal', 'slug' => 'ljusdal', 'latitude' => 61.830839, 'longitude' => 16.08175],
            ['id' => 35, 'county_id' => 4, 'name' => 'Sandviken', 'slug' => 'sandviken', 'latitude' => 60.621607, 'longitude' => 16.775918],
            ['id' => 36, 'county_id' => 4, 'name' => 'Söderhamn', 'slug' => 'soderhamn', 'latitude' => 61.305576, 'longitude' => 17.06281],
            ['id' => 37, 'county_id' => 5, 'name' => 'Falkenberg', 'slug' => 'falkenberg', 'latitude' => 56.902733, 'longitude' => 12.488801],
            ['id' => 38, 'county_id' => 5, 'name' => 'Halmstad', 'slug' => 'halmstad', 'latitude' => 56.674375, 'longitude' => 12.857788],
            ['id' => 39, 'county_id' => 5, 'name' => 'Hyltebruk', 'slug' => 'hyltebruk', 'latitude' => 57.001764, 'longitude' => 13.242099],
            ['id' => 40, 'county_id' => 5, 'name' => 'Kungsbacka', 'slug' => 'kungsbacka', 'latitude' => 57.487492, 'longitude' => 12.076193],
            ['id' => 41, 'county_id' => 5, 'name' => 'Laholm', 'slug' => 'laholm', 'latitude' => 56.505756, 'longitude' => 13.045605],
            ['id' => 42, 'county_id' => 5, 'name' => 'Varberg', 'slug' => 'varberg', 'latitude' => 57.107118, 'longitude' => 12.252091],
            ['id' => 43, 'county_id' => 6, 'name' => 'Bräcke', 'slug' => 'bracke', 'latitude' => 62.750741, 'longitude' => 15.422575],
            ['id' => 44, 'county_id' => 6, 'name' => 'Hammarstrand', 'slug' => 'hammastrand', 'latitude' => 63.112324, 'longitude' => 16.353939],
            ['id' => 45, 'county_id' => 6, 'name' => 'Hammerdal', 'slug' => 'hammerdal', 'latitude' => 63.582716, 'longitude' => 15.351789],
            ['id' => 46, 'county_id' => 6, 'name' => 'Järpen', 'slug' => 'jarpen', 'latitude' => 63.34963, 'longitude' => 13.452908],
            ['id' => 47, 'county_id' => 6, 'name' => 'Strömsund', 'slug' => 'stromsund', 'latitude' => 63.853662, 'longitude' => 15.556869],
            ['id' => 48, 'county_id' => 6, 'name' => 'Sveg', 'slug' => 'sveg', 'latitude' => 62.034625, 'longitude' => 14.359037],
            ['id' => 49, 'county_id' => 6, 'name' => 'Östersund', 'slug' => 'ostersund', 'latitude' => 63.176683, 'longitude' => 14.636068],
            ['id' => 50, 'county_id' => 7, 'name' => 'Bankeryd', 'slug' => 'bankeryd', 'latitude' => 57.857414, 'longitude' => 14.131226],
            ['id' => 51, 'county_id' => 7, 'name' => 'Eksjö', 'slug' => 'eksjo', 'latitude' => 57.665165, 'longitude' => 14.973221],
            ['id' => 52, 'county_id' => 7, 'name' => 'Gislaved', 'slug' => 'gislaved', 'latitude' => 57.2985, 'longitude' => 13.54326],
            ['id' => 53, 'county_id' => 7, 'name' => 'Huskvarna', 'slug' => 'huskvarna', 'latitude' => 57.790414, 'longitude' => 14.275578],
            ['id' => 54, 'county_id' => 7, 'name' => 'Jönköping', 'slug' => 'jonkoping', 'latitude' => 57.782614, 'longitude' => 14.161788],
            ['id' => 55, 'county_id' => 7, 'name' => 'Landsbro', 'slug' => 'landsbro', 'latitude' => 57.366667, 'longitude' => 14.9],
            ['id' => 56, 'county_id' => 7, 'name' => 'Mullsjö', 'slug' => 'mullsjo', 'latitude' => 57.916599, 'longitude' => 13.877317],
            ['id' => 57, 'county_id' => 7, 'name' => 'Nässjö', 'slug' => 'nassjo', 'latitude' => 57.653035, 'longitude' => 14.696725],
            ['id' => 58, 'county_id' => 7, 'name' => 'Skillingaryd', 'slug' => 'skillingaryd', 'latitude' => 57.430582, 'longitude' => 14.09387],
            ['id' => 59, 'county_id' => 7, 'name' => 'Sävsjö', 'slug' => 'savsjo', 'latitude' => 57.398996, 'longitude' => 14.665814],
            ['id' => 60, 'county_id' => 7, 'name' => 'Tranås', 'slug' => 'tranas', 'latitude' => 58.035518, 'longitude' => 14.975696],
            ['id' => 61, 'county_id' => 7, 'name' => 'Vaggeryd', 'slug' => 'vaggeryd', 'latitude' => 57.498962, 'longitude' => 14.14863],
            ['id' => 62, 'county_id' => 7, 'name' => 'Vetlanda', 'slug' => 'vetlanda', 'latitude' => 57.42746, 'longitude' => 15.08533],
            ['id' => 63, 'county_id' => 7, 'name' => 'Värnamo', 'slug' => 'varnamo', 'latitude' => 57.183161, 'longitude' => 14.047821],
            ['id' => 64, 'county_id' => 9, 'name' => 'Alvesta', 'slug' => 'alvesta', 'latitude' => 56.89921, 'longitude' => 14.556001],
            ['id' => 65, 'county_id' => 9, 'name' => 'Braås', 'slug' => 'braas', 'latitude' => 57.062634, 'longitude' => 15.049757],
            ['id' => 66, 'county_id' => 9, 'name' => 'Ljungby', 'slug' => 'ljungby', 'latitude' => 56.833877, 'longitude' => 13.941042],
            ['id' => 67, 'county_id' => 9, 'name' => 'Växjö', 'slug' => 'vaxjo', 'latitude' => 56.879004, 'longitude' => 14.805852],
            ['id' => 68, 'county_id' => 9, 'name' => 'Älmhult', 'slug' => 'almhult', 'latitude' => 56.552446, 'longitude' => 14.137405],
            ['id' => 69, 'county_id' => 10, 'name' => 'Arjeplog', 'slug' => 'arjeplog', 'latitude' => 66.051505, 'longitude' => 17.890054],
            ['id' => 70, 'county_id' => 10, 'name' => 'Arvidsjaur', 'slug' => 'arvidsjaur', 'latitude' => 65.592077, 'longitude' => 19.180283],
            ['id' => 71, 'county_id' => 10, 'name' => 'Boden', 'slug' => 'boden', 'latitude' => 65.825119, 'longitude' => 21.688703],
            ['id' => 72, 'county_id' => 10, 'name' => 'Gällivare', 'slug' => 'gallivare', 'latitude' => 67.1379, 'longitude' => 20.659362],
            ['id' => 73, 'county_id' => 10, 'name' => 'Haparanda', 'slug' => 'haparanda', 'latitude' => 65.841708, 'longitude' => 24.127664],
            ['id' => 74, 'county_id' => 10, 'name' => 'Jokkmokk', 'slug' => 'jokkmokk', 'latitude' => 66.606961, 'longitude' => 19.822921],
            ['id' => 75, 'county_id' => 10, 'name' => 'Kalix', 'slug' => 'kalix', 'latitude' => 65.855281, 'longitude' => 23.143965],
            ['id' => 76, 'county_id' => 10, 'name' => 'Kiruna', 'slug' => 'kiruna', 'latitude' => 67.8558, 'longitude' => 20.225282],
            ['id' => 77, 'county_id' => 10, 'name' => 'Luleå', 'slug' => 'lulea', 'latitude' => 65.584819, 'longitude' => 22.156703],
            ['id' => 78, 'county_id' => 10, 'name' => 'Malmberget', 'slug' => 'malmberget', 'latitude' => 67.173653, 'longitude' => 20.654517],
            ['id' => 79, 'county_id' => 10, 'name' => 'Piteå', 'slug' => 'pitea', 'latitude' => 65.316698, 'longitude' => 21.480036],
            ['id' => 80, 'county_id' => 10, 'name' => 'Älvsbyn', 'slug' => 'alvsbyn', 'latitude' => 65.677136, 'longitude' => 20.992866],
            ['id' => 81, 'county_id' => 10, 'name' => 'Övertornea', 'slug' => 'overtornea', 'latitude' => 66.389725, 'longitude' => 23.649496],
            ['id' => 82, 'county_id' => 11, 'name' => 'Arlöv', 'slug' => 'arlov', 'latitude' => 55.634501, 'longitude' => 13.07441],
            ['id' => 83, 'county_id' => 11, 'name' => 'Bjuv', 'slug' => 'bjuv', 'latitude' => 56.087102, 'longitude' => 12.912505],
            ['id' => 84, 'county_id' => 11, 'name' => 'Broby', 'slug' => 'broby', 'latitude' => 56.254356, 'longitude' => 14.07182],
            ['id' => 85, 'county_id' => 11, 'name' => 'Brösarp', 'slug' => 'brosarp', 'latitude' => 55.716667, 'longitude' => 14.116667],
            ['id' => 86, 'county_id' => 11, 'name' => 'Båstad', 'slug' => 'bastad', 'latitude' => 56.427389, 'longitude' => 12.847722],
            ['id' => 87, 'county_id' => 11, 'name' => 'Dalby', 'slug' => 'dalby', 'latitude' => 55.664437, 'longitude' => 13.348556],
            ['id' => 88, 'county_id' => 11, 'name' => 'Eslöv', 'slug' => 'eslov', 'latitude' => 55.83912, 'longitude' => 13.303391],
            ['id' => 89, 'county_id' => 11, 'name' => 'Helsingborg', 'slug' => 'helsingborg', 'latitude' => 56.046467, 'longitude' => 12.694512],
            ['id' => 90, 'county_id' => 11, 'name' => 'Hässleholm', 'slug' => 'hassleholm', 'latitude' => 56.158914, 'longitude' => 13.766766],
            ['id' => 91, 'county_id' => 11, 'name' => 'Höganäs', 'slug' => 'hoganas', 'latitude' => 56.200639, 'longitude' => 12.555329],
            ['id' => 92, 'county_id' => 11, 'name' => 'Hörby', 'slug' => 'horby', 'latitude' => 55.851716, 'longitude' => 13.661926],
            ['id' => 93, 'county_id' => 11, 'name' => 'Klippan', 'slug' => 'klippan', 'latitude' => 56.1349, 'longitude' => 13.129041],
            ['id' => 94, 'county_id' => 11, 'name' => 'Kristianstad', 'slug' => 'kristianstad', 'latitude' => 56.029394, 'longitude' => 14.156678],
            ['id' => 95, 'county_id' => 11, 'name' => 'Kävlinge', 'slug' => 'kavlinge', 'latitude' => 55.794, 'longitude' => 13.110429],
            ['id' => 96, 'county_id' => 11, 'name' => 'Landskrona', 'slug' => 'landskrona', 'latitude' => 55.870348, 'longitude' => 12.83008],
            ['id' => 97, 'county_id' => 11, 'name' => 'Lund', 'slug' => 'lund', 'latitude' => 55.70466, 'longitude' => 13.191007],
            ['id' => 98, 'county_id' => 11, 'name' => 'Malmö', 'slug' => 'malmo', 'latitude' => 55.604981, 'longitude' => 13.003822],
            ['id' => 99, 'county_id' => 11, 'name' => 'Osby', 'slug' => 'osby', 'latitude' => 56.381536, 'longitude' => 13.992941],
            ['id' => 100, 'county_id' => 11, 'name' => 'Perstorp', 'slug' => 'perstorp', 'latitude' => 56.137972, 'longitude' => 13.394986],
            ['id' => 101, 'county_id' => 11, 'name' => 'Simrishamn', 'slug' => 'simrishamn', 'latitude' => 55.557396, 'longitude' => 14.348965],
            ['id' => 102, 'county_id' => 11, 'name' => 'Sjöbo', 'slug' => 'sjobo', 'latitude' => 55.634822, 'longitude' => 13.70337],
            ['id' => 103, 'county_id' => 11, 'name' => 'Skurup', 'slug' => 'skurup', 'latitude' => 55.48045, 'longitude' => 13.502349],
            ['id' => 104, 'county_id' => 11, 'name' => 'Staffanstorp', 'slug' => 'staffanstorp', 'latitude' => 55.641065, 'longitude' => 13.212229],
            ['id' => 105, 'county_id' => 11, 'name' => 'Svalöv', 'slug' => 'svalov', 'latitude' => 55.91293, 'longitude' => 13.101817],
            ['id' => 106, 'county_id' => 11, 'name' => 'Svedala', 'slug' => 'svedala', 'latitude' => 55.508908, 'longitude' => 13.23711],
            ['id' => 107, 'county_id' => 11, 'name' => 'Södra Sandby', 'slug' => 'sodra_sandby', 'latitude' => 55.716964, 'longitude' => 13.343251],
            ['id' => 108, 'county_id' => 11, 'name' => 'Tomtelilla', 'slug' => 'tomtelilla', 'latitude' => 55.54355, 'longitude' => 13.95484],
            ['id' => 109, 'county_id' => 11, 'name' => 'Trelleborg', 'slug' => 'trelleborg', 'latitude' => 55.376243, 'longitude' => 13.157423],
            ['id' => 110, 'county_id' => 11, 'name' => 'Vellinge', 'slug' => 'vellinge', 'latitude' => 55.473533, 'longitude' => 13.021845],
            ['id' => 111, 'county_id' => 11, 'name' => 'Ystad', 'slug' => 'ystad', 'latitude' => 55.429505, 'longitude' => 13.820031],
            ['id' => 112, 'county_id' => 11, 'name' => 'Åstorp', 'slug' => 'astorp', 'latitude' => 56.134262, 'longitude' => 12.945908],
            ['id' => 113, 'county_id' => 11, 'name' => 'Ängelholm', 'slug' => 'angelholm', 'latitude' => 56.245748, 'longitude' => 12.863881],
            ['id' => 114, 'county_id' => 11, 'name' => 'Örkelljunga', 'slug' => 'orkelljunga', 'latitude' => 56.283635, 'longitude' => 13.278832],
            ['id' => 115, 'county_id' => 12, 'name' => 'Bagarmossen', 'slug' => 'bagarmossen', 'latitude' => 59.274388, 'longitude' => 18.133849],
            ['id' => 116, 'county_id' => 12, 'name' => 'Bandhagen', 'slug' => 'bandhagen', 'latitude' => 59.27, 'longitude' => 18.049444],
            ['id' => 117, 'county_id' => 12, 'name' => 'Bromma', 'slug' => 'bromma', 'latitude' => 59.339783, 'longitude' => 17.939713],
            ['id' => 118, 'county_id' => 12, 'name' => 'Danderyd', 'slug' => 'danderyd', 'latitude' => 59.407905, 'longitude' => 18.019075],
            ['id' => 119, 'county_id' => 12, 'name' => 'Ekerö', 'slug' => 'ekero', 'latitude' => 59.279834, 'longitude' => 17.790225],
            ['id' => 120, 'county_id' => 12, 'name' => 'Enskededalen', 'slug' => 'enskededalen', 'latitude' => 59.283333, 'longitude' => 18.1],
            ['id' => 121, 'county_id' => 12, 'name' => 'Farsta', 'slug' => 'farsta', 'latitude' => 59.244455, 'longitude' => 18.090299],
            ['id' => 122, 'county_id' => 12, 'name' => 'Gustavsberg', 'slug' => 'gustavsberg', 'latitude' => 59.333333, 'longitude' => 18.383333],
            ['id' => 123, 'county_id' => 12, 'name' => 'Handen', 'slug' => 'handen', 'latitude' => 59.166667, 'longitude' => 18.133333],
            ['id' => 124, 'county_id' => 12, 'name' => 'Haninge', 'slug' => 'haninge', 'latitude' => 59.182724, 'longitude' => 18.151091],
            ['id' => 125, 'county_id' => 12, 'name' => 'Huddinge', 'slug' => 'huddinge', 'latitude' => 59.23633, 'longitude' => 17.982156],
            ['id' => 126, 'county_id' => 12, 'name' => 'Hägersten', 'slug' => 'hagersten', 'latitude' => 59.3, 'longitude' => 17.966667],
            ['id' => 127, 'county_id' => 12, 'name' => 'Järfälla', 'slug' => 'jarfalla', 'latitude' => 59.410065, 'longitude' => 17.836804],
            ['id' => 128, 'county_id' => 12, 'name' => 'Kista', 'slug' => 'kista', 'latitude' => 59.402434, 'longitude' => 17.946482],
            ['id' => 129, 'county_id' => 12, 'name' => 'Kungsängen', 'slug' => 'kungsangen', 'latitude' => 59.47772, 'longitude' => 17.750921],
            ['id' => 130, 'county_id' => 12, 'name' => 'Lidingö', 'slug' => 'lidingo', 'latitude' => 59.36296, 'longitude' => 18.1468],
            ['id' => 131, 'county_id' => 12, 'name' => 'Märsta', 'slug' => 'marsta', 'latitude' => 59.619646, 'longitude' => 17.855509],
            ['id' => 132, 'county_id' => 12, 'name' => 'Nacka', 'slug' => 'nacka', 'latitude' => 59.307903, 'longitude' => 18.156042],
            ['id' => 133, 'county_id' => 12, 'name' => 'Norrtälje', 'slug' => 'norrtalje', 'latitude' => 59.759584, 'longitude' => 18.701358],
            ['id' => 134, 'county_id' => 12, 'name' => 'Norsborg', 'slug' => 'norsborg', 'latitude' => 59.24607, 'longitude' => 17.823122],
            ['id' => 135, 'county_id' => 12, 'name' => 'Nynäshamn', 'slug' => 'nynashamn', 'latitude' => 58.902926, 'longitude' => 17.946529],
            ['id' => 136, 'county_id' => 12, 'name' => 'Saltsjö-Boo', 'slug' => 'saltsjoboo', 'latitude' => 59.330717, 'longitude' => 18.286706],
            ['id' => 137, 'county_id' => 12, 'name' => 'Skarpnäck', 'slug' => 'skarpnack', 'latitude' => 59.266667, 'longitude' => 18.116667],
            ['id' => 138, 'county_id' => 12, 'name' => 'Skogås', 'slug' => 'skogas', 'latitude' => 59.216667, 'longitude' => 18.15],
            ['id' => 139, 'county_id' => 12, 'name' => 'Skärholmen', 'slug' => 'skarholmen', 'latitude' => 59.274254, 'longitude' => 17.902301],
            ['id' => 140, 'county_id' => 12, 'name' => 'Sollentuna', 'slug' => 'sollentuna', 'latitude' => 59.43911, 'longitude' => 17.94148],
            ['id' => 141, 'county_id' => 12, 'name' => 'Solna', 'slug' => 'solna', 'latitude' => 59.368879, 'longitude' => 18.008433],
            ['id' => 142, 'county_id' => 12, 'name' => 'Spånga', 'slug' => 'spanga', 'latitude' => 59.382963, 'longitude' => 17.899482],
            ['id' => 143, 'county_id' => 12, 'name' => 'Stockholm', 'slug' => 'stockholm', 'latitude' => 59.329323, 'longitude' => 18.068581],
            ['id' => 144, 'county_id' => 12, 'name' => 'Sundbyberg', 'slug' => 'sundbyberg', 'latitude' => 59.367047, 'longitude' => 17.966309],
            ['id' => 145, 'county_id' => 12, 'name' => 'Södertälje', 'slug' => 'sodertalje', 'latitude' => 59.195363, 'longitude' => 17.625689],
            ['id' => 146, 'county_id' => 12, 'name' => 'Tullinge', 'slug' => 'tullinge', 'latitude' => 59.205278, 'longitude' => 17.903611],
            ['id' => 147, 'county_id' => 12, 'name' => 'Tumba', 'slug' => 'tumba', 'latitude' => 59.199859, 'longitude' => 17.830957],
            ['id' => 148, 'county_id' => 12, 'name' => 'Tyresö', 'slug' => 'tyreso', 'latitude' => 59.242595, 'longitude' => 18.283392],
            ['id' => 149, 'county_id' => 12, 'name' => 'Täby', 'slug' => 'taby', 'latitude' => 59.4419, 'longitude' => 18.07033],
            ['id' => 150, 'county_id' => 12, 'name' => 'Upplands Väsby', 'slug' => 'upplands_vasby', 'latitude' => 59.51961, 'longitude' => 17.92834],
            ['id' => 151, 'county_id' => 12, 'name' => 'Vallentuna', 'slug' => 'vallentuna', 'latitude' => 59.5357, 'longitude' => 18.078017],
            ['id' => 152, 'county_id' => 12, 'name' => 'Vällingby', 'slug' => 'vallingby', 'latitude' => 59.367081, 'longitude' => 17.869143],
            ['id' => 153, 'county_id' => 12, 'name' => 'Västerhaninge', 'slug' => 'vasterhaninge', 'latitude' => 59.12156, 'longitude' => 18.097336],
            ['id' => 154, 'county_id' => 12, 'name' => 'Åkersberga', 'slug' => 'akersberga', 'latitude' => 59.480277, 'longitude' => 18.310783],
            ['id' => 155, 'county_id' => 12, 'name' => 'Älvsjö', 'slug' => 'alvsjo', 'latitude' => 59.274488, 'longitude' => 18.00513],
            ['id' => 156, 'county_id' => 13, 'name' => 'Eskilstuna', 'slug' => 'eskilstuna', 'latitude' => 59.371249, 'longitude' => 16.509804],
            ['id' => 157, 'county_id' => 13, 'name' => 'Flen', 'slug' => 'flen', 'latitude' => 59.057938, 'longitude' => 16.587912],
            ['id' => 158, 'county_id' => 13, 'name' => 'Katrineholm', 'slug' => 'katrineholm', 'latitude' => 58.995551, 'longitude' => 16.205476],
            ['id' => 159, 'county_id' => 13, 'name' => 'Nyköping', 'slug' => 'nykoping', 'latitude' => 58.752844, 'longitude' => 17.009159],
            ['id' => 160, 'county_id' => 13, 'name' => 'Oxelösund', 'slug' => 'oxelosund', 'latitude' => 58.670174, 'longitude' => 17.103733],
            ['id' => 161, 'county_id' => 13, 'name' => 'Strängnäs', 'slug' => 'strangnas', 'latitude' => 59.377452, 'longitude' => 17.032119],
            ['id' => 162, 'county_id' => 13, 'name' => 'Trosa', 'slug' => 'trosa', 'latitude' => 58.898514, 'longitude' => 17.551353],
            ['id' => 163, 'county_id' => 14, 'name' => 'Bålsta', 'slug' => 'balsta', 'latitude' => 59.566903, 'longitude' => 17.530088],
            ['id' => 164, 'county_id' => 14, 'name' => 'Enköping', 'slug' => 'enkoping', 'latitude' => 59.635691, 'longitude' => 17.077823],
            ['id' => 165, 'county_id' => 14, 'name' => 'Skutskär', 'slug' => 'skutskar', 'latitude' => 60.625591, 'longitude' => 17.413558],
            ['id' => 166, 'county_id' => 14, 'name' => 'Tierp', 'slug' => 'tierp', 'latitude' => 60.345896, 'longitude' => 17.516906],
            ['id' => 167, 'county_id' => 14, 'name' => 'Uppsala', 'slug' => 'uppsala', 'latitude' => 59.858564, 'longitude' => 17.638927],
            ['id' => 168, 'county_id' => 15, 'name' => 'Arvika', 'slug' => 'arvika', 'latitude' => 59.654853, 'longitude' => 12.592136],
            ['id' => 169, 'county_id' => 15, 'name' => 'Filipstad', 'slug' => 'filipstad', 'latitude' => 59.713997, 'longitude' => 14.169844],
            ['id' => 170, 'county_id' => 15, 'name' => 'Hagfors', 'slug' => 'hagfors', 'latitude' => 60.03437, 'longitude' => 13.694508],
            ['id' => 171, 'county_id' => 15, 'name' => 'Karlstad', 'slug' => 'karlstad', 'latitude' => 59.379136, 'longitude' => 13.500804],
            ['id' => 172, 'county_id' => 15, 'name' => 'Kristinehamn', 'slug' => 'kristinehamn', 'latitude' => 59.310068, 'longitude' => 14.108919],
            ['id' => 173, 'county_id' => 15, 'name' => 'Sunne', 'slug' => 'sunne', 'latitude' => 59.836557, 'longitude' => 13.144046],
            ['id' => 174, 'county_id' => 15, 'name' => 'Säffle', 'slug' => 'saffle', 'latitude' => 59.132661, 'longitude' => 12.930107],
            ['id' => 175, 'county_id' => 15, 'name' => 'Torsby', 'slug' => 'torsby', 'latitude' => 60.140907, 'longitude' => 13.010213],
            ['id' => 176, 'county_id' => 15, 'name' => 'Årjäng', 'slug' => 'arjang', 'latitude' => 59.389159, 'longitude' => 12.132717],
            ['id' => 177, 'county_id' => 16, 'name' => 'Burträsk', 'slug' => 'burtrask', 'latitude' => 64.519791, 'longitude' => 20.656677],
            ['id' => 178, 'county_id' => 16, 'name' => 'Lycksele', 'slug' => 'lycksele', 'latitude' => 64.59581, 'longitude' => 18.676367],
            ['id' => 179, 'county_id' => 16, 'name' => 'Nordmaling', 'slug' => 'nordmaling', 'latitude' => 63.566667, 'longitude' => 19.5],
            ['id' => 180, 'county_id' => 16, 'name' => 'Robertsfors', 'slug' => 'robertsfors', 'latitude' => 64.191835, 'longitude' => 20.84891],
            ['id' => 181, 'county_id' => 16, 'name' => 'Skellefteå', 'slug' => 'skelleftea', 'latitude' => 64.750244, 'longitude' => 20.950917],
            ['id' => 182, 'county_id' => 16, 'name' => 'Umeå', 'slug' => 'umea', 'latitude' => 63.825847, 'longitude' => 20.263035],
            ['id' => 183, 'county_id' => 16, 'name' => 'Vilhelmina', 'slug' => 'vilhelmina', 'latitude' => 64.624471, 'longitude' => 16.655497],
            ['id' => 184, 'county_id' => 16, 'name' => 'Vindeln', 'slug' => 'vindeln', 'latitude' => 64.201953, 'longitude' => 19.71887],
            ['id' => 185, 'county_id' => 16, 'name' => 'Vännäs', 'slug' => 'vannas', 'latitude' => 63.908007, 'longitude' => 19.752965],
            ['id' => 186, 'county_id' => 17, 'name' => 'Fränsta', 'slug' => 'fransta', 'latitude' => 62.49787, 'longitude' => 16.169515],
            ['id' => 187, 'county_id' => 17, 'name' => 'Härnösand', 'slug' => 'harnosand', 'latitude' => 62.63227, 'longitude' => 17.940871],
            ['id' => 188, 'county_id' => 17, 'name' => 'Kramfors', 'slug' => 'kramfors', 'latitude' => 62.928433, 'longitude' => 17.786295],
            ['id' => 189, 'county_id' => 17, 'name' => 'Sollefteå', 'slug' => 'solleftea', 'latitude' => 63.165407, 'longitude' => 17.277135],
            ['id' => 190, 'county_id' => 17, 'name' => 'Sundsvall', 'slug' => 'sundsvall', 'latitude' => 62.390811, 'longitude' => 17.306927],
            ['id' => 191, 'county_id' => 17, 'name' => 'Sörberge', 'slug' => 'sorberge', 'latitude' => 62.51, 'longitude' => 17.371389],
            ['id' => 192, 'county_id' => 17, 'name' => 'Timrå', 'slug' => 'timra', 'latitude' => 62.485456, 'longitude' => 17.324865],
            ['id' => 193, 'county_id' => 17, 'name' => 'Ånge', 'slug' => 'ange', 'latitude' => 62.522874, 'longitude' => 15.658942],
            ['id' => 194, 'county_id' => 17, 'name' => 'Örnsköldsvik', 'slug' => 'ornskoldsvik', 'latitude' => 63.290047, 'longitude' => 18.716617],
            ['id' => 195, 'county_id' => 18, 'name' => 'Arboga', 'slug' => 'arboga', 'latitude' => 59.393688, 'longitude' => 15.838175],
            ['id' => 196, 'county_id' => 18, 'name' => 'Fagersta', 'slug' => 'fagersta', 'latitude' => 59.989144, 'longitude' => 15.816642],
            ['id' => 197, 'county_id' => 18, 'name' => 'Hallstahammar', 'slug' => 'hallstahammar', 'latitude' => 59.613204, 'longitude' => 16.229476],
            ['id' => 198, 'county_id' => 18, 'name' => 'Kolbäck', 'slug' => 'kolback', 'latitude' => 59.565326, 'longitude' => 16.235419],
            ['id' => 199, 'county_id' => 18, 'name' => 'Kolsva', 'slug' => 'kolsva', 'latitude' => 59.600307, 'longitude' => 15.841819],
            ['id' => 200, 'county_id' => 18, 'name' => 'Kungsör', 'slug' => 'kungsor', 'latitude' => 59.422397, 'longitude' => 16.097786],
            ['id' => 201, 'county_id' => 18, 'name' => 'Köping', 'slug' => 'koping', 'latitude' => 59.512096, 'longitude' => 15.99451],
            ['id' => 202, 'county_id' => 18, 'name' => 'Norberg', 'slug' => 'norberg', 'latitude' => 60.065042, 'longitude' => 15.923796],
            ['id' => 203, 'county_id' => 18, 'name' => 'Sala', 'slug' => 'sala', 'latitude' => 59.920859, 'longitude' => 16.606328],
            ['id' => 204, 'county_id' => 18, 'name' => 'Västerås', 'slug' => 'vasteras', 'latitude' => 59.6099, 'longitude' => 16.544809],
            ['id' => 205, 'county_id' => 19, 'name' => 'Alingsås', 'slug' => 'alingsas', 'latitude' => 57.930021, 'longitude' => 12.536211],
            ['id' => 206, 'county_id' => 19, 'name' => 'Angered', 'slug' => 'angered', 'latitude' => 57.79915, 'longitude' => 12.05272],
            ['id' => 207, 'county_id' => 19, 'name' => 'Bengtsfors', 'slug' => 'bengtsfors', 'latitude' => 59.028612, 'longitude' => 12.226943],
            ['id' => 208, 'county_id' => 19, 'name' => 'Borås', 'slug' => 'boras', 'latitude' => 57.721035, 'longitude' => 12.939819],
            ['id' => 209, 'county_id' => 19, 'name' => 'Falköping', 'slug' => 'falkoping', 'latitude' => 58.175029, 'longitude' => 13.553217],
            ['id' => 210, 'county_id' => 19, 'name' => 'Grästorp', 'slug' => 'grastorp', 'latitude' => 58.33404, 'longitude' => 12.680218],
            ['id' => 211, 'county_id' => 19, 'name' => 'Göteborg', 'slug' => 'goteborg', 'latitude' => 57.70887, 'longitude' => 11.97456],
            ['id' => 212, 'county_id' => 19, 'name' => 'Herrljunga', 'slug' => 'herrljunga', 'latitude' => 58.078029, 'longitude' => 13.018839],
            ['id' => 213, 'county_id' => 19, 'name' => 'Hjo', 'slug' => 'hjo', 'latitude' => 58.30707, 'longitude' => 14.287466],
            ['id' => 214, 'county_id' => 19, 'name' => 'Hunnebostrand', 'slug' => 'hunnebostrand', 'latitude' => 58.442148, 'longitude' => 11.301577],
            ['id' => 215, 'county_id' => 19, 'name' => 'Kinna', 'slug' => 'kinna', 'latitude' => 57.50984, 'longitude' => 12.694132],
            ['id' => 216, 'county_id' => 19, 'name' => 'Kungälv', 'slug' => 'kungalv', 'latitude' => 57.869754, 'longitude' => 11.974032],
            ['id' => 217, 'county_id' => 19, 'name' => 'Kållered', 'slug' => 'kallered', 'latitude' => 57.608487, 'longitude' => 12.066008],
            ['id' => 218, 'county_id' => 19, 'name' => 'Lerum', 'slug' => 'lerum', 'latitude' => 57.769484, 'longitude' => 12.26882],
            ['id' => 219, 'county_id' => 19, 'name' => 'Lidköping', 'slug' => 'lidkoping', 'latitude' => 58.503505, 'longitude' => 13.157077],
            ['id' => 220, 'county_id' => 19, 'name' => 'Lilla edet', 'slug' => 'lilla_edet', 'latitude' => 58.132158, 'longitude' => 12.124134],
            ['id' => 221, 'county_id' => 19, 'name' => 'Lysekil', 'slug' => 'lysekil', 'latitude' => 58.275573, 'longitude' => 11.435558],
            ['id' => 222, 'county_id' => 19, 'name' => 'Mariestad', 'slug' => 'mariestad', 'latitude' => 58.710112, 'longitude' => 13.821333],
            ['id' => 223, 'county_id' => 19, 'name' => 'Mellerud', 'slug' => 'mellerud', 'latitude' => 58.702568, 'longitude' => 12.451842],
            ['id' => 224, 'county_id' => 19, 'name' => 'Mölndal', 'slug' => 'molndal', 'latitude' => 57.65, 'longitude' => 12.016667],
            ['id' => 225, 'county_id' => 19, 'name' => 'Mölnlycke', 'slug' => 'molnlycke', 'latitude' => 57.658163, 'longitude' => 12.118993],
            ['id' => 226, 'county_id' => 19, 'name' => 'Nödinge', 'slug' => 'nodinge', 'latitude' => 57.483333, 'longitude' => 12.516667],
            ['id' => 227, 'county_id' => 19, 'name' => 'Partille', 'slug' => 'partille', 'latitude' => 57.761857, 'longitude' => 12.134718],
            ['id' => 228, 'county_id' => 19, 'name' => 'Skara', 'slug' => 'skara', 'latitude' => 58.386013, 'longitude' => 13.439328],
            ['id' => 229, 'county_id' => 19, 'name' => 'Skene', 'slug' => 'skene', 'latitude' => 57.483333, 'longitude' => 12.633333],
            ['id' => 230, 'county_id' => 19, 'name' => 'Skövde', 'slug' => 'skovde', 'latitude' => 58.390278, 'longitude' => 13.846121],
            ['id' => 231, 'county_id' => 19, 'name' => 'Stenungsund', 'slug' => 'stenungsund', 'latitude' => 58.067839, 'longitude' => 11.829435],
            ['id' => 232, 'county_id' => 19, 'name' => 'Strömstad', 'slug' => 'stromstad', 'latitude' => 58.938346, 'longitude' => 11.179187],
            ['id' => 233, 'county_id' => 19, 'name' => 'Svenljunga', 'slug' => 'svenljunga', 'latitude' => 57.495572, 'longitude' => 13.114622],
            ['id' => 234, 'county_id' => 19, 'name' => 'Tanumshede', 'slug' => 'tanumshede', 'latitude' => 58.723994, 'longitude' => 11.325239],
            ['id' => 235, 'county_id' => 19, 'name' => 'Tidaholm', 'slug' => 'tidaholm', 'latitude' => 58.181769, 'longitude' => 13.959474],
            ['id' => 236, 'county_id' => 19, 'name' => 'Torslanda', 'slug' => 'torslanda', 'latitude' => 57.723152, 'longitude' => 11.767669],
            ['id' => 237, 'county_id' => 19, 'name' => 'Tranemo', 'slug' => 'tranemo', 'latitude' => 57.485569, 'longitude' => 13.35241],
            ['id' => 238, 'county_id' => 19, 'name' => 'Trollhättan', 'slug' => 'trollhattan', 'latitude' => 58.283489, 'longitude' => 12.285821],
            ['id' => 239, 'county_id' => 19, 'name' => 'Töreboda', 'slug' => 'toreboda', 'latitude' => 58.705509, 'longitude' => 14.126147],
            ['id' => 240, 'county_id' => 19, 'name' => 'Uddevalla', 'slug' => 'uddevalla', 'latitude' => 58.3498, 'longitude' => 11.935649],
            ['id' => 241, 'county_id' => 19, 'name' => 'Ulricehamn', 'slug' => 'ulricehamn', 'latitude' => 57.783333, 'longitude' => 13.416667],
            ['id' => 242, 'county_id' => 19, 'name' => 'Vara', 'slug' => 'vara', 'latitude' => 58.261781, 'longitude' => 12.960194],
            ['id' => 243, 'county_id' => 19, 'name' => 'Vänersborg', 'slug' => 'vanersborg', 'latitude' => 58.379728, 'longitude' => 12.324803],
            ['id' => 244, 'county_id' => 19, 'name' => 'Åmål', 'slug' => 'amal', 'latitude' => 59.051117, 'longitude' => 12.697732],
            ['id' => 245, 'county_id' => 20, 'name' => 'Askersund', 'slug' => 'askersund', 'latitude' => 58.889427, 'longitude' => 14.910987],
            ['id' => 246, 'county_id' => 20, 'name' => 'Degerfors', 'slug' => 'degerfors', 'latitude' => 59.239103, 'longitude' => 14.433918],
            ['id' => 247, 'county_id' => 20, 'name' => 'Hallsberg', 'slug' => 'hallsberg', 'latitude' => 59.066532, 'longitude' => 15.10229],
            ['id' => 248, 'county_id' => 20, 'name' => 'Karlskoga', 'slug' => 'karlskoga', 'latitude' => 59.328634, 'longitude' => 14.536414],
            ['id' => 249, 'county_id' => 20, 'name' => 'Kumla', 'slug' => 'kumla', 'latitude' => 59.126536, 'longitude' => 15.140105],
            ['id' => 250, 'county_id' => 20, 'name' => 'Laxå', 'slug' => 'laxa', 'latitude' => 58.98269, 'longitude' => 14.62289],
            ['id' => 251, 'county_id' => 20, 'name' => 'Lindesberg', 'slug' => 'lindesberg', 'latitude' => 59.597698, 'longitude' => 15.222911],
            ['id' => 252, 'county_id' => 20, 'name' => 'Örebro', 'slug' => 'orebro', 'latitude' => 59.275263, 'longitude' => 15.213411],
            ['id' => 253, 'county_id' => 21, 'name' => 'Finspång', 'slug' => 'finspang', 'latitude' => 58.707488, 'longitude' => 15.773595],
            ['id' => 254, 'county_id' => 21, 'name' => 'Kisa', 'slug' => 'kisa', 'latitude' => 57.987026, 'longitude' => 15.631203],
            ['id' => 255, 'county_id' => 21, 'name' => 'Linköping', 'slug' => 'linkoping', 'latitude' => 58.410807, 'longitude' => 15.621373],
            ['id' => 256, 'county_id' => 21, 'name' => 'Mjölby', 'slug' => 'mjolby', 'latitude' => 58.322691, 'longitude' => 15.133535],
            ['id' => 257, 'county_id' => 21, 'name' => 'Motala', 'slug' => 'motala', 'latitude' => 58.538034, 'longitude' => 15.047094],
            ['id' => 258, 'county_id' => 21, 'name' => 'Norrköping', 'slug' => 'norrkoping', 'latitude' => 58.587745, 'longitude' => 16.192421],
            ['id' => 259, 'county_id' => 21, 'name' => 'Söderköping', 'slug' => 'soderkoping', 'latitude' => 58.475901, 'longitude' => 16.323431],
            ['id' => 260, 'county_id' => 21, 'name' => 'Vadstena', 'slug' => 'vadstena', 'latitude' => 58.447602, 'longitude' => 14.890234],
            ['id' => 261, 'county_id' => 21, 'name' => 'Åtvidaberg', 'slug' => 'atvidaberg', 'latitude' => 58.20219, 'longitude' => 15.99727],
            ['id' => 264, 'county_id' => 9, 'name' => 'Åseda', 'slug' => 'aseda', 'latitude' => 57.167180, 'longitude' => 15.346627],
        ];
    }
}