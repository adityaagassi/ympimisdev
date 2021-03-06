<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFloLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flo_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('flo_number');
            $table->string('status_code');
            $table->integer('created_by');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['flo_number', 'status_code'], 'flo_log_unique');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flo_logs');
    }
}
