<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_songs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('userid');   //用户id
            $table->integer('Songid');   //歌曲总库id
            $table->integer('totalDownload')->default(0);   //下载次数
            $table->tinyInteger('inWarehouse')->default(0);   //是否入库  0:未入库，1已入库
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_songs');
    }
}
