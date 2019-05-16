<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitorDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_visitor');
            $table->string('id_number');
            $table->string('full_name');
            $table->string('in_time');
            $table->string('out_time');
            $table->string('status');
            $table->string('tag');
            $table->string('remark');
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
        Schema::dropIfExists('visitor_details');
    }
}
