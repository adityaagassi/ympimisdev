<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHeaderBensukisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('header_bensukis', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model');
            $table->string('kode_op_bensuki');
            $table->string('nik_op_bensuki');
            $table->string('kode_op_plate');
            $table->string('nik_op_plate');
            $table->string('shift');
            $table->string('mesin');
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
        Schema::dropIfExists('header_bensukis');
    }
}
