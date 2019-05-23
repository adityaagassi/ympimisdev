<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogNgMiddlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_ng_middles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('group_code');
            $table->string('op_kensa');
            $table->date('prod_date');
            $table->string('tag');
            $table->string('material_number');
            $table->string('location');
            $table->string('ng_name');
            $table->double('qty');
            $table->string('op_prod');
            $table->double('remark')->nullable();
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
        Schema::dropIfExists('log_ng_middles');
    }
}
