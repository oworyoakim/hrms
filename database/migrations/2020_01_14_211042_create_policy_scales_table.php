<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePolicyScalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('policy_scales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('leave_policy_id');
            $table->unsignedBigInteger('salary_scale_id');
            $table->boolean('active')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['leave_policy_id','salary_scale_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('policy_scales');
    }
}
