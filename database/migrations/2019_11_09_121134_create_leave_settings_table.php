<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('leave_type_id');
            $table->unsignedInteger('duration');
            $table->boolean('with_weekend')->default(false);
            $table->boolean('earned_leave')->default(false);
            $table->boolean('carry_forward')->default(false);
            $table->unsignedInteger('max_carry_forward_duration')->default(0);
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
        Schema::dropIfExists('leave_settings');
    }
}
