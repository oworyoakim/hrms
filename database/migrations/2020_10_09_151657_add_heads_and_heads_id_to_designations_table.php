<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHeadsAndHeadsIdToDesignationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('designations', function (Blueprint $table) {
            $table->enum('heads', [
                'executive-director-office',
                'directorate',
                'department',
                'division',
                'section',
            ])
                  ->after('active')
                  ->nullable();

            $table->unsignedBigInteger('heads_id')
                  ->nullable()
                  ->after('heads');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('designations', function (Blueprint $table) {
            $table->dropColumn(['heads', 'heads_id']);
        });
    }
}
