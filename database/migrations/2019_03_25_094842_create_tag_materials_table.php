<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_materials', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tag')->unique();
            $table->string('material_number');
            $table->double('quantity');
            $table->string('op_prod');
            $table->string('location');
            $table->string('remark')->nullable();
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
        Schema::dropIfExists('tag_materials');
    }
}
