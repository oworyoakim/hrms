<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelatedPersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('related_persons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('dob')->nullable();
            $table->string('nin')->nullable();
            $table->enum('gender',['male','female','other'])->nullable();
            $table->enum('title',['Mr','Mrs','Miss','Ms','Dr','Prof','Eng','Hon'])->nullable();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedInteger('relationship_id')->nullable();
            $table->boolean('emergency')->default(false);
            $table->boolean('dependant')->default(false);
            $table->boolean('is_next_of_kin')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('related_persons');
    }
}
