<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerformanceStandardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('performance_standards', function (Blueprint $table) {
            $table->id();
            $table->string('factor');
            $table->text('question');
            $table->string('excellent')->comment('Score of 5');
            $table->string('veryGood')->comment('Score of 4');
            $table->string('good')->comment('Score of 3');
            $table->string('satisfactory')->comment('Score of 2');
            $table->string('unsatisfactory')
                  ->default('Fails to meet the required standards.')
                  ->comment('Score of 1');
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
        Schema::dropIfExists('performance_standards');
    }
}
