<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDangersongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('danger_songs', function (Blueprint $table) {
            $table->string('name');
            $table->string('singer');
            $table->string('language');
            $table->string('recordcompany');
            $table->integer('musicdbpk');
            $table->timestamp('uploadDateStr');

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
        Schema::dropIfExists('danger_songs');
    }
}
