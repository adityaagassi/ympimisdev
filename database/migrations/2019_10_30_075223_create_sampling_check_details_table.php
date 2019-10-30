<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSamplingCheckDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sampling_check_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sampling_check_id');
            $table->string('point_check');
            $table->string('hasil_check');
            $table->string('picture_check');
            $table->string('pic_check');
            $table->string('sampling_by');
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
        Schema::dropIfExists('sampling_check_details');
    }
}
