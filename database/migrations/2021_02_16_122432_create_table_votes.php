<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableVotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',500);
            $table->unsignedBigInteger('id_operator');
            $table->string('value',256);
            $table->dateTime('start');
            $table->dateTime('end');
            $table->boolean('status')->default(0);
            $table->foreign('id_operator')->references('id')->on('operators');

            $table->timestamps();
        });



        Schema::create('logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('action');
            $table->unsignedBigInteger('id_operator');
            $table->string('model', 45)->default('vote');
            $table->unsignedBigInteger('fk')->nullable();
            $table->unsignedBigInteger('id_pc');
            $table->boolean('status')->default(0);

            $table->foreign('id_operator')->references('id')->on('operators');

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
        Schema::dropIfExists('logs');
        Schema::dropIfExists('tokens');
    }
}
