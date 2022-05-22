<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableFile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->string('key', 40)->nullable();
            $table->string('model', 20)->default('vote')->nullable();
            $table->unsignedInteger('fk')->nullable();
            $table->string('url', 250);
            $table->string('type', 15)->default('jpg');
            $table->timestamps();
        });


        Schema::create('levels', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->string('title', 80);
            $table->string('options', 500)->nullable();
            $table->string('explain', 250)->nullable();
            $table->timestamps();
        });




        Schema::create('operators', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->string('nick_name', 110);
            $table->unsignedBigInteger('user_id');

            $table->unsignedBigInteger('level_id');
            $table->unsignedBigInteger('id_pc');
            $table->string('model', 45)->default('city');
            $table->unsignedBigInteger('fk')->nullable();
            $table->string('more', 500)->nullable();
            $table->string('last_open_app', 30)->nullable();

            $table->string('mac_address')->nullable();
            $table->string('device_model', 40)->default('no find');
            $table->tinyInteger('version_install')->default(0);
            $table->tinyInteger('status')->default(0);

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('level_id')->references('id')->on('levels');
            $table->timestamps();
        });




    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operators');
        Schema::dropIfExists('levels');
        Schema::dropIfExists('files');

    }
}
