<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('addressable_id');
            $table->string('addressable_type');
            $table->string('country')->default('Uganda');
            $table->string('region');
            $table->string('subregion');
            $table->string('district');
            $table->string('subcounty');
            $table->string('parish');
            $table->string('village');
            $table->string('street')->nullable();
            $table->string('plot')->nullable();
            $table->string('building_name')->nullable();
            $table->string('floor')->nullable();
            $table->string('room')->nullable();
            $table->enum('type',['permanent','residence','work']);
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
        Schema::dropIfExists('addresses');
    }
}
