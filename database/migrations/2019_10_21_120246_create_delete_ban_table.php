<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeleteBanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delete_ban', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userId');
            $table->integer('banId');
            $table->string('explain')->nullable();  
            $table->timestamp('time');  
            $table->string('songnum',20);   //歌曲编号
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delete_ban');
    }
}
