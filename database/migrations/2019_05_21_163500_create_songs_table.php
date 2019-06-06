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
            $table->integer('Songid');
            $table->primary('Songid'); //总库id
            $table->string('Songnum')->default('')->nullable();
            $table->string('SongName')->default('')->nullable();
            $table->string('Singer')->default('')->nullable();
            $table->string('SongNameAlias')->default('')->nullable();
            $table->string('SingerAlias')->default('')->nullable();
            $table->tinyInteger('Langtype')->default(0)->nullable();
            $table->tinyInteger('Songtype')->default(0)->nullable();
            $table->tinyInteger('Songmark')->default(0)->nullable();
            $table->tinyInteger('SoundType')->default(0)->nullable();
            $table->string('AlbumName')->default('')->nullable();
            $table->string('Pinyin')->default('')->nullable();
            $table->string('AllPinyin')->default('')->nullable();
            $table->integer('Wordcount')->default(0)->nullable(); //歌曲字数
            $table->string('FistWordStrokes')->default('')->nullable();//歌曲的首字笔画
            $table->string('Strokes')->default('')->nullable(); //歌曲的笔画
            $table->tinyInteger('IssueAreaID')->default(0)->nullable(); //发行地区
            $table->tinyInteger('SongCustomTypes')->default(0)->nullable();
            $table->string('RecordCompany')->default('')->nullable();
            $table->tinyInteger('singerArea')->default(0)->nullable();
            $table->string('SingerPinyin')->default('')->nullable();
            $table->string('SingerAllPinyin')->default('')->nullable();
            $table->string('SingerSex')->default('')->nullable();
            $table->string('SingerBH')->default('')->nullable(); //歌星笔画
            $table->string('SingerOneWorkBH')->default('')->nullable();
            $table->dateTime('UploadDate')->default(null)->nullable();
            $table->integer('videoClass')->default(0)->nullable();
            $table->string('Filename')->default('')->nullable();
            $table->float('FileSize')->default(0)->nullable();
            $table->integer('SongMid')->default(0)->nullable();

            $table->charset = 'utf8';
            $table->engine = 'InnoDB';
            $table->collation = 'utf8_unicode_ci';
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
