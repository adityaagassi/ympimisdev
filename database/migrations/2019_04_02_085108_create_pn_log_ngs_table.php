<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePnLogNgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pn_log_ngs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ng');
            $table->integer('line');
            $table->string('operator');
            $table->string('tag');
            $table->string('model');
            $table->string('location');
            $table->integer('qty');           
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
        Schema::dropIfExists('pn_log_ngs');
    }
}
