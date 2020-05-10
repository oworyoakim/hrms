<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('employee_number')->unique();
            $table->string('username')->unique();
            $table->unsignedBigInteger('user_id')->unique();
            $table->enum('title',['Mr','Mrs','Miss','Ms','Dr','Prof','Eng','Hon']);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('salary_scale_id')->nullable();
            $table->unsignedBigInteger('designation_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('division_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('directorate_id')->nullable();
            $table->enum('gender',['male','female','other'])->nullable();
            $table->unsignedBigInteger('marital_status_id')->nullable();
            $table->unsignedInteger('religion_id')->nullable();
            $table->date('dob')->nullable();
            $table->string('nin')->nullable();
            $table->string('passport')->nullable();
            $table->string('nssf')->nullable();
            $table->string('tin')->nullable();
            $table->string('permit')->nullable();
            $table->date('date_joined')->nullable();
            $table->string('avatar')->default('avatar.png');
            $table->boolean('approved')->default(false);
            $table->date('exit_date')->nullable();
            $table->string('nationality')->nullable();
            $table->enum('employee_status',['active','exited','suspended','onleave'])->default('active');
            $table->enum('employment_term',['permanent','contract'])->default('contract');
            $table->enum('employment_status',['probation','confirmed'])->default('probation');
            $table->enum('employment_type',['full-time','part-time'])->default('full-time');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
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
        Schema::dropIfExists('employees');
    }
}
