<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInterviewDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interview_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('interview_id');
            $table->string('nik');
            $table->string('filosofi_yamaha');
            $table->string('aturan_k3');
            $table->string('komitmen_berkendara');
            $table->string('kebijakan_mutu');
            $table->string('dasar_tindakan_bekerja');
            $table->string('enam_pasal_keselamatan');
            $table->string('budaya_kerja');
            $table->string('budaya_5s');
            $table->string('komitmen_hotel_konsep');
            $table->string('janji_tindakan_dasar');
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
        Schema::dropIfExists('interview_details');
    }
}
