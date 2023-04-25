<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('counties', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->double('lat');
            $table->double('lng');
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('counties')->insert($this->getCounties());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::table('counties')->delete();
        Schema::dropIfExists('counties');
    }

    protected function getCounties()
    {
        return [
            ['id' => 1,    'name' => 'Blekinge', 'slug' => 'blekinge_lan', 'lat' => 56.1431090, 'lng' => 15.3666703],
            ['id' => 2,    'name' => 'Dalarnas', 'slug' => 'dalarnas_lan', 'lat' => 61.0917012, 'lng' => 14.6663653],
            ['id' => 3,    'name' => 'Gotlands', 'slug' => 'gotland', 'lat' => 57.5312910, 'lng' => 18.6901396],
            ['id' => 4,    'name' => 'Gävleborgs', 'slug' => 'gavleborgs_lan', 'lat' => 61.4388743, 'lng' => 16.5940196],
            ['id' => 5,    'name' => 'Hallands', 'slug' => 'hallands_lan', 'lat' => 56.7957699, 'lng' => 12.8934930],
            ['id' => 6,    'name' => 'Jämtlands', 'slug' => 'jamtlands_lan', 'lat' => 63.1711922, 'lng' => 14.9591800],
            ['id' => 7,    'name' => 'Jönköpings', 'slug' => 'jonkopings_lan', 'lat' => 57.3708434, 'lng' => 14.3439173],
            ['id' => 8,    'name' => 'Kalmar', 'slug' => 'kalmar_lan', 'lat' => 57.0942447, 'lng' => 16.5435605],
            ['id' => 9,    'name' => 'Kronobergs', 'slug' => 'kronobergs_lan', 'lat' => 56.8906494, 'lng' => 14.5084523],
            ['id' => 10,    'name' => 'Norrbottens', 'slug' => 'norrbottens_lan', 'lat' => 66.8309216, 'lng' => 20.3991966],
            ['id' => 11,    'name' => 'Skåne', 'slug' => 'skane_lan', 'lat' => 55.9902572, 'lng' => 13.5957692],
            ['id' => 12,    'name' => 'Stockholms', 'slug' => 'stockholms_lan', 'lat' => 59.3327881, 'lng' => 18.0644881],
            ['id' => 13,    'name' => 'Södermanlands', 'slug' => 'sodermanlands_lan', 'lat' => 59.0336349, 'lng' => 16.7518899],
            ['id' => 14,    'name' => 'Uppsala', 'slug' => 'uppsala_lan', 'lat' => 60.2724233, 'lng' => 18.1396124],
            ['id' => 15,    'name' => 'Värmlands', 'slug' => 'varmlands_lan', 'lat' => 59.5807897, 'lng' => 12.8472974],
            ['id' => 16,    'name' => 'Västerbottens', 'slug' => 'vasterbottens_lan', 'lat' => 65.3337311, 'lng' => 16.5161695],
            ['id' => 17,    'name' => 'Västernorrlands', 'slug' => 'vasternorrlands_lan', 'lat' => 62.9855290, 'lng' => 18.1258604],
            ['id' => 18,    'name' => 'Västmanlands', 'slug' => 'vastmanlands_lan', 'lat' => 59.6713879, 'lng' => 16.2158954],
            ['id' => 19,    'name' => 'Västragötalands', 'slug' => 'vastra_gotalands', 'lat' => 58.1599159, 'lng' => 12.1360549],
            ['id' => 20,    'name' => 'Örebro', 'slug' => 'orebro_lan', 'lat' => 59.5350360, 'lng' => 15.0065731],
            ['id' => 21,    'name' => 'Östergötlands', 'slug' => 'ostergotlands_lan', 'lat' => 58.3453635, 'lng' => 15.5197843],
        ];
    }
}
