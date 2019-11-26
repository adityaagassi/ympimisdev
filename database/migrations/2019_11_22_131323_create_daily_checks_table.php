<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailyChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_checks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_list_id');
            $table->string('department');
            $table->string('product');
            $table->date('production_date');
            $table->date('check_date');
            $table->string('serial_number');
            $table->string('condition');
            $table->string('keterangan');
            $table->string('leader');
            $table->string('foreman');
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
        Schema::dropIfExists('daily_checks');
    }
}
