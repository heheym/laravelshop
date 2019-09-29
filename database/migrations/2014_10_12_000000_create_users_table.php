<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->tinyInteger('vipState')->default(0);  //会员状态
            $table->tinyInteger('vipXgStartDay')->default(7);  //
            $table->timestamp('vipStartTime');  //会员开始时间
            $table->timestamp('vipTime');  //会员到期时间
            $table->string('api_token', 60)->unique()->nullable();
            $table->tinyInteger('download')->default(1);  //会员到期时间
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
