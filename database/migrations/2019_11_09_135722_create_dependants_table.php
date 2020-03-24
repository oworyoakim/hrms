<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDependantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dependants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('title',['Mr','Mrs','Miss','Ms','Dr','Prof','Eng','Hon'])->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('dob');
            $table->string('nin')->nullable();
            $table->enum('gender',['male','female','other'])->nullable();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedInteger('relationship_id');
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
        Schema::dropIfExists('dependants');
    }
}
