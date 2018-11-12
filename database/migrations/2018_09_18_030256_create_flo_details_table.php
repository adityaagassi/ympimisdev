<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFloDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flo_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('serial_number');
            $table->string('material_number');
            $table->string('origin_group_code');
            $table->string('flo_number');
            $table->string('completion')->nullable();
            $table->string('transfer')->nullable();
            $table->double('quantity');
            $table->integer('created_by');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['serial_number', 'origin_group_code'], 'flo_detail_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flo_details');
    }
}
