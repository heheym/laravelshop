<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('add_songs', function (Blueprint $table) {
            $table->increments('bugeId');
            $table->string('singer',100);
            $table->string('songname',100);
            $table->integer('userid')->nullable();
            $table->integer('musicdbpk')->nullable();
            $table->tinyInteger('state')->nullable();
            $table->string('source',100)->nullable();
            $table->string('explain',200)->nullable();
            $table->timestamp('date');

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
        Schema::dropIfExists('add_songs');
    }
}
