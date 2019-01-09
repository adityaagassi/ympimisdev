<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_processes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('process_code');
            $table->string('serial_number');
            $table->string('model')->nullable();
            $table->double('quantity')->default('1');
            $table->integer('created_by');
            $table->integer('manpower');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['serial_number', 'model', 'process_code'], 'log_process_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_processes');
    }
}