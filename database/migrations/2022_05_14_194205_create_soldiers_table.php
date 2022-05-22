<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoldiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    

    public function up(){

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('personnel_id',15);
            $table->string('national_id',10);
            $table->string('name',30);
            $table->string('last_name',50);
            $table->string('father_name',30);
            $table->string('unit_name',30);
            $table->string('company_name',30);
            $table->string('job_name',30);
            $table->string('military_rank',30);
            $table->string('date_of_birth',40);
            $table->string('place_of_issue',30);
            $table->integer('weight')->nullable();
            $table->integer('height')->nullable();
            $table->string('blood_type',5)->nullable();
            $table->string('bank_account_number',30)->nullable();
            $table->string('home_address');
            $table->integer('access_type')->nullable();
            $table->timestamps();
        });

        Schema::create('soldiers', function (Blueprint $table) {
            $table->id();
            $table->string('personnel_id',15);
            $table->string('national_id',10);
            $table->string('name',30);
            $table->string('last_name',50);
            $table->string('unit_name',30);
            $table->string('company_name',30);
            $table->string('job_name',30);
            $table->string('military_rank',30);
            $table->string('father_name',30); 
            $table->string('date_of_birth',40);
            $table->string('place_of_issue',30);
            $table->integer('weight')->nullable();
            $table->integer('height')->nullable();
            $table->string('blood_type',5)->nullable();
            $table->string('bank_account_number',30)->nullable();
            $table->string('home_address');
            
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->string('personnel_id_sender',15);
            $table->string('personnel_id_receiver',15);
            $table->longText('comment');
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
        Schema::dropIfExists('comments');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('soldiers');
    }
}
