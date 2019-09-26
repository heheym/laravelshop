<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->primary('musicdbpk'); //总库id
            $table->string('name')->default('')->nullable();
            $table->string('singer')->default('')->nullable();
            $table->tinyInteger('singerType')->default(0)->nullable();
            $table->tinyInteger('location')->default(0)->nullable();
            $table->string('namePingyin')->default('')->nullable();
            $table->string('nameFullPingYing')->default('')->nullable();
            $table->string('nameCharacts')->default('')->nullable();
            $table->integer('nameWordLenght')->default(1)->nullable();
            $table->integer('nameCharactsCount')->default(0)->nullable();
            $table->string('singerNameFirst')->default('')->nullable();
            $table->string('singerPingYin')->default('')->nullable();
            $table->string('singerFullPingYing')->default('')->nullable();
            $table->string('singerLocation')->default('')->nullable();
            $table->string('singerGender')->default('')->nullable();
            $table->string('singerCharacts')->default('')->nullable();
            $table->string('chineseName')->default('')->nullable();
            $table->integer('size')->default(0)->nullable();
            $table->tinyInteger('language')->default(0)->nullable();
            $table->tinyInteger('videoClass')->default(0)->nullable();
            $table->string('recordCompany')->default('')->nullable();
            $table->string('album')->default('')->nullable();
            $table->tinyInteger('copyRight')->default(0)->nullable();
            $table->tinyInteger('category')->default(0)->nullable();
            $table->tinyInteger('type')->default(0)->nullable();
            $table->tinyInteger('format')->default(0)->nullable();
            $table->timestamp('uploadDateStr')->nullable();
            $table->tinyInteger('audioClass')->default(0)->nullable();
            $table->tinyInteger('isTaste')->default(0)->nullable();
            $table->tinyInteger('isApp')->default(0)->nullable();
            $table->string('sedName')->default('')->nullable();
            $table->string('thiName')->default('')->nullable();
            $table->string('localPath')->default('')->nullable();
            $table->string('bugeId')->default('')->nullable();
            $table->string('isRealCopy')->default('')->nullable();
            $table->string('searchName1')->default('')->nullable();
            $table->string('searchName2')->default('')->nullable();
            $table->string('word')->default('')->nullable();
            $table->string('introduce')->default('')->nullable();
            $table->string('hasLogo')->default('')->nullable();
            $table->integer('ranking')->default(0)->nullable();
            $table->integer('musicdbpk')->default(0)->nullable();
            $table->integer('musicMid')->default(0)->nullable();
            $table->string('bscoin')->default(0)->nullable();
            $table->string('ispf')->default('0')->nullable();
            $table->string('isbsHide')->default('0')->nullable();
            $table->string('variety')->default('')->nullable();
            $table->tinyInteger('isbver')->default(0)->nullable();
            $table->string('songnum')->default('')->nullable();


            $table->charset = 'utf8';
            $table->engine = 'InnoDB';
            $table->collation = 'utf8_general_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('songs');
    }
}
