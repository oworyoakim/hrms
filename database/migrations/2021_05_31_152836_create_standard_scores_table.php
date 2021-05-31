<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandardScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('standard_scores', function (Blueprint $table) {
            $table->unsignedBigInteger('appraisalId');
            $table->unsignedBigInteger('standardId');
            $table->unsignedFloat('score');
            $table->timestamps();
            $table->primary(['appraisalId', 'standardId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('standard_scores');
    }
}
