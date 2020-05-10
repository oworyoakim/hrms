<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDelegationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delegations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('substantive_id');
            $table->unsignedBigInteger('delegated_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->text('reason')->nullable();
            $table->text('permissions')->nullable();
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
        Schema::dropIfExists('delegations');
    }
}
