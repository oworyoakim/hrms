<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDesignationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('designations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->unsignedBigInteger('directorate_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('division_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('salary_scale_id');
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->boolean('probational')->default(true);
            $table->unsignedTinyInteger('probation_period')->nullable();
            $table->text('summary')->nullable();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('max_holders')->default(1);
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('designations');
    }
}
