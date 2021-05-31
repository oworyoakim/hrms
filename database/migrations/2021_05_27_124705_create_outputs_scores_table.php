<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutputsScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outputs_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appraisalId');
            $table->unsignedBigInteger('outputId');
            $table->unsignedFloat('maximumScore');
            $table->unsignedFloat('selfScore');
            $table->unsignedFloat('appraiserRating');
            $table->unsignedFloat('agreedScore');
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
        Schema::dropIfExists('outputs_scores');
    }
}
