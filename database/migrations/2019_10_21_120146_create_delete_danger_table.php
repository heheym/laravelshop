<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;




class CreateDeleteDangerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delete_danger', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userId');
            $table->integer('dangerId');
            $table->integer('musicId');
            $table->string('explain')->nullable();
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delete_danger');
    }
}
