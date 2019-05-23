<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbsenceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absence_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('employee_id');
            $table->date('absence_date');
            $table->string('absence_code');
            $table->string('remark');
            $table->integer('created_by');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['employee_id', 'absence_date'], 'absence_log_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absence_logs');
    }
}
