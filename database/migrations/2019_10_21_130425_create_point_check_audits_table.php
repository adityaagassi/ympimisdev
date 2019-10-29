<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointCheckAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('point_check_audits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_list_id');
            $table->string('product');
            $table->string('proses');
            $table->string('point_check');
            $table->string('cara_cek');
            $table->string('created_by');
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
        Schema::dropIfExists('point_check_audits');
    }
}
