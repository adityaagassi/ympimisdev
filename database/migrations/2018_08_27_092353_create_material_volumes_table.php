<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialVolumesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_volumes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('material_number')->unique();
            $table->string('category');
            $table->double('lot_completion');
            $table->double('lot_transfer');
            $table->double('lot_pallet');
            $table->double('lot_volume');
            $table->double('length');
            $table->double('width');
            $table->double('height');
            $table->integer('created_by');
            $table->softDeletes();
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
        Schema::dropIfExists('material_volumes');
    }
}
