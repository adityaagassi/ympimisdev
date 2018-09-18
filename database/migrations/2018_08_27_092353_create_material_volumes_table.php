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
            $table->double('lot_completion')->default('0');
            $table->double('lot_transfer')->default('0');;
            $table->double('lot_flo')->default('0');;
            $table->double('lot_row')->default('0');;
            $table->double('lot_pallet')->default('0');;
            $table->double('lot_carton')->default('0');;
            $table->double('length')->default('0');;
            $table->double('width')->default('0');;
            $table->double('height')->default('0');;
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
