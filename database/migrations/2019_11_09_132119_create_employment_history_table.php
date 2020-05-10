<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmploymentHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employment_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('action_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('from_designation_id')->nullable();
            $table->unsignedBigInteger('to_designation_id')->nullable();
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('employment_history');
    }
}
