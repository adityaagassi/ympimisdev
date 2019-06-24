<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBarrelLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barrel_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('machine');
            $table->string('tag');
            $table->string('material_number');
            $table->double('qty');
            $table->string('status');
            $table->time('duration');
            $table->string('remark');
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
        Schema::dropIfExists('barrel_logs');
    }
}
