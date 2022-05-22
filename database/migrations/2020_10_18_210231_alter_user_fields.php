<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table)
        {
            $table->dropColumn(['name']);
            $table->string('phone_number', 11)->after('id')->unique();
            $table->string('first_name', 100)->after('phone_number')->nullable();
            $table->string('last_name', 100)->after('first_name')->nullable();
            $table->string('username', 50)->after('last_name')->unique()->nullable();
            $table->unsignedTinyInteger('status')->after('username')->default(0);
            $table->string('api_token', 191)->after('status')->unique();
        });

        Schema::create('tmp_registers', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('phone_number', 11);
            $table->tinyInteger('status')->default(0);
            $table->string('type', 50);
            $table->string('explain', 50)->nullable();
            $table->integer('code')->unsigned();
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
        Schema::dropIfExists('tmp_registers');
        Schema::table('users', function (Blueprint $table)
        {
            $table->dropColumn(['phone_number', 'first_name', 'last_name', 'username', 'status', 'android_id', 'type', 'image', 'api_token']);
            $table->string('name');
        });

    }
}
