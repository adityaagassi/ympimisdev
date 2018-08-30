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
            $table->string('material_number');
            $table->string('category');
            $table->string('type');
            $table->double('lot');
            $table->double('length');
            $table->double('width');
            $table->double('height');
            $table->integer('created_by');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['material_number', 'type']);
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
